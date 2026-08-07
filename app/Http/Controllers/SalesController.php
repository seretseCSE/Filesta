<?php

namespace App\Http\Controllers;

use App\Models\DailySession;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        return view('sales.index', [
            'session' => $this->currentSession(),
            'items' => Item::orderBy('name')->get(['id', 'name', 'sale_price']),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
            'todaySales' => Sale::with(['item', 'paymentMethod'])
                ->where('user_id', $userId)
                ->where('sale_date', today()->toDateString())
                ->latest('id')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
        ]);

        DB::transaction(function () use ($data, &$sale) {
            $item = Item::query()->lockForUpdate()->findOrFail($data['item_id']);

            if ($item->stock_quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$item->stock_quantity} unit(s) left in stock.",
                ]);
            }

            $item->decrement('stock_quantity', $data['quantity']);

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'quantity' => $data['quantity'],
                'unit_cost' => $item->cost_price,
                'unit_price' => $item->sale_price,
                'payment_method_id' => $data['payment_method_id'],
                'sale_date' => today()->toDateString(),
            ]);
        });

        return redirect()->route('sales.index')->with('status', 'Sale logged.');
    }

    public function edit(Sale $sale): View
    {
        $this->authorizeEditableSale($sale);

        return view('sales.edit', [
            'sale' => $sale->load('item'),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $this->authorizeEditableSale($sale);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
        ]);

        DB::transaction(function () use ($data, $sale) {
            $item = Item::query()->lockForUpdate()->findOrFail($sale->item_id);

            $delta = $data['quantity'] - $sale->quantity;

            if ($delta > 0 && $item->stock_quantity < $delta) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$item->stock_quantity} additional unit(s) in stock.",
                ]);
            }

            $item->increment('stock_quantity', -$delta);

            $sale->update([
                'quantity' => $data['quantity'],
                'payment_method_id' => $data['payment_method_id'],
            ]);
        });

        return redirect()->route('sales.index')->with('status', 'Sale updated.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->authorizeEditableSale($sale);

        DB::transaction(function () use ($sale) {
            $item = Item::query()->lockForUpdate()->findOrFail($sale->item_id);
            $item->increment('stock_quantity', $sale->quantity);
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('status', 'Sale deleted, stock restored.');
    }

    public function blocked(): View
    {
        return view('sales.blocked');
    }

    public function close(): View
    {
        $userId = auth()->id();

        $todayRevenue = Sale::where('user_id', $userId)
            ->where('sale_date', today()->toDateString())
            ->get()
            ->sum(fn ($sale) => $sale->quantity * $sale->unit_price);

        $salesmen = \App\Models\User::where('role', \App\Enums\UserRole::Salesman)
            ->with('sales')
            ->get();

        $salesmen = $salesmen->map(function ($salesman) {
            $salesman->total_revenue = $salesman->sales->sum(fn ($s) => $s->quantity * $s->unit_price);
            return $salesman;
        })->sortByDesc('total_revenue')->values();

        $cumulativeRevenue = $salesmen->firstWhere('id', $userId)?->total_revenue ?? 0;
        $rank = $salesmen->search(fn ($salesman) => $salesman->id === $userId);
        $rank = $rank === false ? $salesmen->count() + 1 : $rank + 1;

        return view('sales.close', compact('todayRevenue', 'cumulativeRevenue', 'rank'));
    }

    public function storeClose(): RedirectResponse
    {
        $session = $this->currentSession();
        if ($session) {
            $session->update([
                'closed_at' => now(),
                'is_active' => false,
            ]);
        }

        return redirect()->route('sales.closed');
    }

    public function closed(): View
    {
        return view('sales.closed');
    }

    private function currentSession(): ?DailySession
    {
        return DailySession::query()
            ->where('user_id', auth()->id())
            ->where('date', today()->toDateString())
            ->first();
    }

    private function sessionIsOpen(): bool
    {
        return $this->currentSession()?->is_active ?? false;
    }

    private function authorizeEditableSale(Sale $sale): void
    {
        abort_unless($sale->user_id === auth()->id(), 403);
        abort_unless($sale->sale_date->isToday(), 403);
        abort_unless($this->sessionIsOpen(), 403);
    }
}
