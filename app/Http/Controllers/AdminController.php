<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\DailySession;
use App\Models\Item;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $dateRange = $request->input('date_range', 'today');
        $salesmanId = $request->input('salesman_id', 'all');

        $salesQuery = Sale::query();

        if ($dateRange === 'today') {
            $salesQuery->whereDate('sale_date', today());
        } elseif ($dateRange === 'week') {
            $salesQuery->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
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
            ->selectRaw('user_id, SUM(quantity * unit_price) as revenue, SUM(quantity) as items_sold')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('revenue')
            ->get();

        $itemBreakdown = (clone $salesQuery)
            ->selectRaw('item_id, SUM(quantity * unit_price) as revenue, SUM(quantity) as items_sold')
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
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'pin' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'pin' => $data['pin'],
            'role' => UserRole::Salesman,
        ]);

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
}
