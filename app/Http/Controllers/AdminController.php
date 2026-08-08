<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Exports\ReportsExport;
use App\Models\DailySession;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $dateRange = $request->input('date_range', 'all');
        $salesmanId = $request->input('salesman_id', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $salesQuery = Sale::query();

        if ($dateRange === 'today') {
            $salesQuery->whereDate('sale_date', today());
        } elseif ($dateRange === 'week') {
            $salesQuery->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $salesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        } else {
            $dateRange = 'all';
        }

        if ($salesmanId !== 'all') {
            $salesQuery->where('user_id', $salesmanId);
        }

        $summary = (clone $salesQuery)->selectRaw('
            SUM(quantity * unit_price) as total_revenue,
            SUM(quantity * unit_cost) as total_cost,
            SUM(quantity) as items_sold
        ')->first();

        $totalRevenue = $summary->total_revenue ?? 0;
        $totalCost = $summary->total_cost ?? 0;
        $totalProfit = $totalRevenue - $totalCost;
        $itemsSold = $summary->items_sold ?? 0;

        $salesmanBreakdown = (clone $salesQuery)
            ->selectRaw('user_id, SUM(quantity * unit_price) as revenue, SUM(quantity) as items_sold, SUM(quantity * (unit_price - unit_cost)) as profit')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('revenue')
            ->get();

        $itemBreakdown = (clone $salesQuery)
            ->selectRaw('item_id, SUM(quantity * unit_price) as revenue, SUM(quantity) as items_sold, SUM(quantity * (unit_price - unit_cost)) as profit')
            ->groupBy('item_id')
            ->with('item')
            ->orderByDesc('revenue')
            ->get();

        $paymentBreakdown = (clone $salesQuery)
            ->selectRaw('payment_method_id, SUM(quantity * unit_price) as revenue')
            ->groupBy('payment_method_id')
            ->with('paymentMethod')
            ->orderByDesc('revenue')
            ->get();

        $lowStockItems = Item::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->get();

        $salesmen = User::where('role', UserRole::Salesman)->orderBy('name')->get();

        return view('admin.dashboard', compact(
            'dateRange',
            'salesmanId',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'itemsSold',
            'salesmanBreakdown',
            'itemBreakdown',
            'paymentBreakdown',
            'lowStockItems',
            'salesmen'
        ));
    }

    public function exportReports(Request $request): BinaryFileResponse
    {
        $dateRange = $request->input('date_range', 'all');
        $salesmanId = $request->input('salesman_id', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($dateRange === 'custom' && !($startDate && $endDate)) {
            $dateRange = 'all';
        }

        return Excel::download(new ReportsExport($dateRange, $salesmanId, $startDate, $endDate), 'reports.xlsx');
    }

    public function salesmen(): View
    {
        $salesmen = User::where('role', UserRole::Salesman)
            ->withAggregate('sales as revenue', 'SUM(quantity * unit_price)')
            ->get()
            ->each(fn (User $salesman) => $salesman->revenue = (float) ($salesman->revenue ?? 0))
            ->sortByDesc('revenue')
            ->values();

        $today = today()->toDateString();
        $todaySessions = DailySession::query()
            ->whereIn('user_id', $salesmen->pluck('id'))
            ->where('date', $today)
            ->get()
            ->keyBy('user_id');

        $salesmen->each(function (User $salesman, int $index) use ($todaySessions) {
            $salesman->rank = $index + 1;
            $salesman->today_session = $todaySessions->get($salesman->id);
        });

        return view('admin.salesmen', ['salesmen' => $salesmen]);
    }

    public function storeSalesman(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        try {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => UserRole::Salesman,
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()
                ->withErrors(['email' => 'A salesman with this email already exists.'])
                ->onlyInput('name', 'email');
        }

        return redirect()->route('admin.salesmen')->with('status', 'Salesman added.');
    }

    public function activateSalesman(User $user): RedirectResponse
    {
        abort_unless($user->isSalesman(), 404);

        DailySession::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()->toDateString()],
            ['activated_at' => now(), 'closed_at' => null, 'is_active' => true]
        );

        return back()->with('status', "{$user->name} is active for today.");
    }

    public function deactivateSalesman(User $user): RedirectResponse
    {
        abort_unless($user->isSalesman(), 404);

        DailySession::query()
            ->where('user_id', $user->id)
            ->where('date', today()->toDateString())
            ->update(['is_active' => false, 'closed_at' => now()]);

        return back()->with('status', "{$user->name} deactivated for today.");
    }

    public function updateSalesmanPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isSalesman(), 404);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:4'],
        ]);

        $user->update(['password' => $data['password']]);

        return back()->with('status', "Password updated for {$user->name}.");
    }

    public function destroySalesman(User $user): RedirectResponse
    {
        abort_unless($user->isSalesman(), 404);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['salesman' => 'You cannot delete your own account.']);
        }

        if ($user->sales()->exists()) {
            return back()->withErrors(['salesman' => "Cannot delete {$user->name}: sales history exists for this account."]);
        }

        DB::transaction(function () use ($user) {
            $user->dailySessions()->delete();
            $user->delete();
        });

        return redirect()->route('admin.salesmen')->with('status', "{$user->name} deleted.");
    }

    public function items(): View
    {
        return view('admin.items', ['items' => Item::orderBy('name')->get()]);
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ]);

        Item::create($data);

        return redirect()->route('admin.items')->with('status', 'Item added.');
    }

    public function editItem(Item $item): View
    {
        return view('admin.items-edit', ['item' => $item]);
    }

    public function updateItem(Request $request, Item $item): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ]);

        $item->update($data);

        return redirect()->route('admin.items')->with('status', 'Item updated.');
    }

    public function destroyItem(Item $item): RedirectResponse
    {
        if ($item->sales()->exists()) {
            return back()->withErrors(['item' => "Cannot delete {$item->name}: sales history exists for this item."]);
        }

        $item->delete();

        return redirect()->route('admin.items')->with('status', 'Item deleted.');
    }

    public function restockItem(Request $request, Item $item): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item->increment('stock_quantity', $data['quantity']);

        return back()->with('status', "Restocked {$item->name} by {$data['quantity']} unit(s).");
    }

    public function sales(Request $request): View
    {
        $dateRange = $request->input('date_range', 'all');
        $salesmanId = $request->input('salesman_id', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $salesQuery = Sale::with(['item', 'user', 'paymentMethod']);

        if ($dateRange === 'today') {
            $salesQuery->whereDate('sale_date', today());
        } elseif ($dateRange === 'week') {
            $salesQuery->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $salesQuery->whereBetween('sale_date', [$startDate, $endDate]);
        } else {
            $dateRange = 'all';
        }

        if ($salesmanId !== 'all') {
            $salesQuery->where('user_id', $salesmanId);
        }

        $sales = $salesQuery->latest('sale_date')->latest('id')->paginate(50)->withQueryString();
        $salesmen = User::where('role', UserRole::Salesman)->orderBy('name')->get();

        return view('admin.sales', compact('sales', 'salesmen', 'dateRange', 'salesmanId', 'startDate', 'endDate'));
    }

    public function editSale(Sale $sale): View
    {
        return view('admin.sales-edit', [
            'sale' => $sale->load(['item', 'user', 'paymentMethod']),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
        ]);
    }

    public function updateSale(Request $request, Sale $sale): RedirectResponse
    {
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

        return redirect()->route('admin.sales')->with('status', 'Sale updated.');
    }

    public function destroySale(Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($sale) {
            $item = Item::query()->lockForUpdate()->findOrFail($sale->item_id);
            $item->increment('stock_quantity', $sale->quantity);
            $sale->delete();
        });

        return redirect()->route('admin.sales')->with('status', 'Sale deleted, stock restored.');
    }
}
