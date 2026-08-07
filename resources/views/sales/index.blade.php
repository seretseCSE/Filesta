@extends('layouts.app')

@section('title', 'Log a sale')
@section('page-title', 'Log a sale')

@php
    $itemOptions = $items->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'price' => (float) $item->sale_price,
        'stock' => (int) $item->stock_quantity,
    ])->values();
@endphp

@section('app-bar-actions')
    @if ($session?->activated_at)
        <span style="font-size:0.75rem;color:#94a3b8;">Since {{ $session->activated_at->format('g:i A') }}</span>
    @endif
    @if ($session?->is_active)
        <a href="{{ route('sales.close') }}" data-offline-disable style="color:#f87171;font-size:0.875rem;font-weight:600;background:none;border:none;cursor:pointer;text-decoration:none;">Close day</a>
    @endif
@endsection

@section('content')
    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('sales.store') }}" id="sale-form">
        @csrf

        <div class="field">
            <label class="field-label">Item</label>
            <input
                type="text"
                id="item-search"
                class="form-input"
                placeholder="Search items…"
                value="{{ old('item_name') }}"
                autocomplete="off"
            >
            <input type="hidden" name="item_id" id="item-id" value="{{ old('item_id') }}">
            <div id="item-results" class="hidden overflow-hidden rounded-md border border-gray-300 bg-white shadow-sm"></div>
            <p id="item-pick-error" class="hidden text-sm text-red-600">Please pick an item from the list.</p>
        </div>

        <div class="field">
            <label class="field-label">Quantity</label>
            @include('sales._stepper')
        </div>

        <div class="field" style="margin-bottom:0;">
            <label class="field-label">Payment method</label>
            <select name="payment_method_id" required class="form-select">
                @foreach ($paymentMethods as $method)
                    <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                        {{ $method->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <p class="section-title">Today's sales</p>

    @forelse ($todaySales as $sale)
        <div class="list-row">
            <div style="min-width:0;">
                <div class="list-row-title">{{ $sale->item->name }}</div>
                <div class="list-row-sub">{{ $sale->quantity }} × ETB {{ number_format($sale->unit_price, 2) }} · {{ $sale->paymentMethod->name }}</div>
                <div class="list-row-amount">ETB {{ number_format($sale->quantity * $sale->unit_price, 2) }}</div>
            </div>
            @if ($session?->is_active)
                <div class="list-actions">
                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale?')" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ghost-red">Delete</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <p style="text-align:center;color:#94a3b8;padding:32px 0;font-size:0.875rem;">No sales yet today.</p>
    @endforelse
@endsection

@section('bottom-bar')
    <button type="submit" form="sale-form" data-offline-disable class="btn btn-indigo">Log sale</button>
@endsection

@push('scripts')
    <script>
        (function () {
            var items = @json($itemOptions);
            var search = document.getElementById('item-search');
            var hidden = document.getElementById('item-id');
            var results = document.getElementById('item-results');
            var error = document.getElementById('item-pick-error');

            function select(item) {
                hidden.value = item.id;
                search.value = item.name;
                error.classList.add('hidden');
                results.classList.add('hidden');
                results.innerHTML = '';
            }

            function render(q) {
                var query = q.trim().toLowerCase();
                if (!query) {
                    results.classList.add('hidden');
                    results.innerHTML = '';
                    return;
                }
                var matches = items.filter(function (item) {
                    return item.name.toLowerCase().indexOf(query) !== -1;
                });
                results.innerHTML = '';
                if (!matches.length) {
                    var empty = document.createElement('div');
                    empty.className = 'px-3 py-3 text-sm text-gray-400';
                    empty.textContent = 'No matching items.';
                    results.appendChild(empty);
                } else {
                    matches.forEach(function (item) {
                        var btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'flex w-full items-center justify-between gap-2 px-3 py-3 text-left text-base active:bg-gray-100';
                        var stockColor = item.stock > 0 ? 'text-gray-400' : 'text-red-500 font-semibold';
                        var stockText = item.stock > 0 ? 'Stock: ' + item.stock : 'Out of stock';
                        btn.innerHTML =
                            '<span class="min-w-0"><span class="block font-medium text-gray-900">' + item.name + '</span>' +
                            '<span class="block text-sm ' + stockColor + '">' + stockText + '</span></span>' +
                            '<span class="shrink-0 font-semibold text-gray-700">ETB ' + item.price.toFixed(2) + '</span>';
                        btn.addEventListener('click', function () { select(item); });
                        results.appendChild(btn);
                    });
                }
                results.classList.remove('hidden');
            }

            search.addEventListener('input', function () {
                if (hidden.value) {
                    hidden.value = '';
                }
                render(search.value);
            });
            search.addEventListener('focus', function () { render(search.value); });

            if (hidden.value) {
                var found = items.find(function (item) { return String(item.id) === String(hidden.value); });
                if (found) select(found);
            }

            document.getElementById('sale-form').addEventListener('submit', function (e) {
                if (!hidden.value) {
                    e.preventDefault();
                    error.classList.remove('hidden');
                    search.focus();
                }
            });
        })();
    </script>
@endpush
