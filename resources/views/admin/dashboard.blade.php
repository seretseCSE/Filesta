@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
    <div class="mx-auto max-w-2xl px-4 pt-6 pb-6">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-lg font-bold">Dashboard</h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.salesmen') }}" class="text-sm text-indigo-600">Salesmen</a>
                <a href="{{ route('admin.items') }}" class="text-sm text-indigo-600">Items</a>
                <a href="{{ route('admin.sales') }}" class="text-sm text-indigo-600">Ledger</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500">Sign out</button>
                </form>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-4 flex flex-wrap gap-3">
            <select name="date_range" onchange="this.form.submit()"
                class="rounded border border-gray-300 px-3 py-2 text-base focus:border-indigo-500 focus:outline-none">
                <option value="today" @selected($dateRange === 'today')>Today</option>
                <option value="week" @selected($dateRange === 'week')>This Week</option>
                <option value="all" @selected($dateRange === 'all')>All Time</option>
            </select>
            <select name="salesman_id" onchange="this.form.submit()"
                class="rounded border border-gray-300 px-3 py-2 text-base focus:border-indigo-500 focus:outline-none">
                <option value="all" @selected($salesmanId === 'all')>All Salesmen</option>
                @foreach($salesmen as $salesman)
                    <option value="{{ $salesman->id }}" @selected($salesmanId == $salesman->id)>{{ $salesman->name }}</option>
                @endforeach
            </select>
            <button type="submit" formaction="{{ route('admin.reports.export') }}"
                class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 active:bg-gray-100">
                Export Excel
            </button>
        </form>

        {{-- Summary cards --}}
        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Revenue</p>
                <p class="mt-1 text-xl font-bold">ETB {{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="rounded border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Cost</p>
                <p class="mt-1 text-xl font-bold">ETB {{ number_format($totalCost, 2) }}</p>
            </div>
            <div class="rounded border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Profit</p>
                <p class="mt-1 text-xl font-bold text-green-600">ETB {{ number_format($totalProfit, 2) }}</p>
            </div>
            <div class="rounded border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Items Sold</p>
                <p class="mt-1 text-xl font-bold">{{ number_format($itemsSold) }}</p>
            </div>
        </div>

        {{-- Salesman breakdown --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 font-semibold">By Salesman</h2>
        <div class="mt-2 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs text-gray-500">
                        <th class="py-2 pr-4 font-medium">Name</th>
                        <th class="py-2 pr-4 text-right font-medium">Items</th>
                        <th class="py-2 pr-4 text-right font-medium">Revenue</th>
                        <th class="py-2 text-right font-medium">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($salesmanBreakdown as $row)
                        <tr>
                            <td class="py-2 pr-4">{{ $row->user->name }}</td>
                            <td class="py-2 pr-4 text-right text-gray-500">{{ number_format($row->items_sold) }}</td>
                            <td class="py-2 pr-4 text-right">{{ number_format($row->revenue, 2) }}</td>
                            <td class="py-2 text-right text-green-600">{{ number_format($row->profit, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-400">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Item breakdown --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 font-semibold">By Item</h2>
        <div class="mt-2 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs text-gray-500">
                        <th class="py-2 pr-4 font-medium">Item</th>
                        <th class="py-2 pr-4 text-right font-medium">Qty</th>
                        <th class="py-2 pr-4 text-right font-medium">Revenue</th>
                        <th class="py-2 text-right font-medium">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($itemBreakdown as $row)
                        <tr>
                            <td class="py-2 pr-4">{{ $row->item->name }}</td>
                            <td class="py-2 pr-4 text-right text-gray-500">{{ number_format($row->items_sold) }}</td>
                            <td class="py-2 pr-4 text-right">{{ number_format($row->revenue, 2) }}</td>
                            <td class="py-2 text-right text-green-600">{{ number_format($row->profit, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-400">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Payment method breakdown --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 font-semibold">By Payment Method</h2>
        <div class="mt-2 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs text-gray-500">
                        <th class="py-2 pr-4 font-medium">Method</th>
                        <th class="py-2 text-right font-medium">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paymentBreakdown as $row)
                        <tr>
                            <td class="py-2 pr-4">{{ $row->paymentMethod->name }}</td>
                            <td class="py-2 text-right">{{ number_format($row->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="py-4 text-center text-gray-400">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Low stock --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 font-semibold text-red-600">Low Stock Alert</h2>
        <div class="mt-2 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-red-100 text-left text-xs text-gray-500">
                        <th class="py-2 pr-4 font-medium">Item</th>
                        <th class="py-2 pr-4 text-right font-medium">Stock</th>
                        <th class="py-2 text-right font-medium">Threshold</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-100">
                    @forelse($lowStockItems as $item)
                        <tr>
                            <td class="py-2 pr-4 font-medium">{{ $item->name }}</td>
                            <td class="py-2 pr-4 text-right font-bold text-red-600">{{ $item->stock_quantity }}</td>
                            <td class="py-2 text-right text-gray-500">{{ $item->low_stock_threshold }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-400">All items are well stocked</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
