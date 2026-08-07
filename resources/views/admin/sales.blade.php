@extends('layouts.app')

@section('title', 'Sales ledger')
@section('page-title', 'Sales ledger')

@section('content')
    <div class="mx-auto max-w-2xl px-4 pt-6 pb-6">

        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-lg font-bold">Sales ledger</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600">← Dashboard</a>
        </div>

        @if (session('status'))
            <div class="mt-3 rounded border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.sales') }}" class="mt-4 flex flex-wrap gap-3">
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
        </form>

        {{-- Sales list --}}
        <ul class="mt-4 flex flex-col divide-y divide-gray-100">
            @forelse($sales as $sale)
                <li class="flex items-start justify-between gap-3 py-3">
                    <div class="min-w-0">
                        <p class="font-medium">{{ $sale->item->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $sale->user->name }} · {{ $sale->sale_date->format('M j, Y') }}
                        </p>
                        <p class="text-sm text-gray-700">
                            {{ $sale->quantity }} × ETB {{ number_format($sale->unit_price, 2) }} = ETB {{ number_format($sale->quantity * $sale->unit_price, 2) }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $sale->paymentMethod->name }}</p>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <a href="{{ route('admin.sales.edit', $sale) }}" class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 active:bg-gray-100">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded border border-red-200 px-3 py-1.5 text-sm text-red-600 active:bg-red-50">
                                Delete
                            </button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="py-8 text-center text-sm text-gray-400">No sales found.</li>
            @endforelse
        </ul>

        <div class="mt-4">{{ $sales->links() }}</div>
    </div>
@endsection
