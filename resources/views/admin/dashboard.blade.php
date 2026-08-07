@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mx-auto max-w-5xl px-4 pt-12 pb-12">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Admin Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">Signed in as {{ auth()->user()->name }}.</p>
            </div>
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('admin.salesmen') }}"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-indigo-600 active:bg-gray-50"
                >
                    Manage salesmen
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 active:bg-gray-100">Sign out</button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-8 flex flex-col gap-4 sm:flex-row">
            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Date Range</span>
                <select name="date_range" onchange="this.form.submit()" class="rounded-xl border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="today" @selected($dateRange === 'today')>Today</option>
                    <option value="week" @selected($dateRange === 'week')>This Week</option>
                    <option value="all" @selected($dateRange === 'all')>All Time</option>
                </select>
            </label>
            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Salesman</span>
                <select name="salesman_id" onchange="this.form.submit()" class="rounded-xl border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <option value="all" @selected($salesmanId === 'all')>All Salesmen</option>
                    @foreach($salesmen as $salesman)
                        <option value="{{ $salesman->id }}" @selected($salesmanId == $salesman->id)>{{ $salesman->name }}</option>
                    @endforeach
                </select>
            </label>
        </form>

        <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Total Revenue</dt>
                <dd class="mt-2 text-2xl font-bold tracking-tight text-gray-900">ETB {{ number_format($totalRevenue, 2) }}</dd>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Total Cost</dt>
                <dd class="mt-2 text-2xl font-bold tracking-tight text-gray-900">ETB {{ number_format($totalCost, 2) }}</dd>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Total Profit</dt>
                <dd class="mt-2 text-2xl font-bold tracking-tight text-green-600">ETB {{ number_format($totalProfit, 2) }}</dd>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <dt class="text-sm font-medium text-gray-500">Items Sold</dt>
                <dd class="mt-2 text-2xl font-bold tracking-tight text-gray-900">{{ number_format($itemsSold) }}</dd>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            <div>
                <h2 class="text-lg font-bold">Salesman Breakdown</h2>
                <div class="mt-3 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-900">Salesman</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Items Sold</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Revenue (ETB)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($salesmanBreakdown as $row)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">{{ $row->user->name }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ number_format($row->items_sold) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900">{{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold">Item Breakdown</h2>
                <div class="mt-3 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-900">Item</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Quantity Sold</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Revenue (ETB)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($itemBreakdown as $row)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">{{ $row->item->name }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ number_format($row->items_sold) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900">{{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold">Payment Methods</h2>
                <div class="mt-3 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-900">Method</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Revenue (ETB)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($paymentBreakdown as $row)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">{{ $row->paymentMethod->name }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900">{{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-center text-gray-500">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <span class="text-red-500">&uarr;</span> Low Stock Alert
                </h2>
                <div class="mt-3 overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-red-200 text-left text-sm">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-900">Item</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Current Stock</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-900">Threshold</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-200 bg-white">
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-right text-red-600 font-bold">{{ $item->stock_quantity }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ $item->low_stock_threshold }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">All items are well stocked</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
