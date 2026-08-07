@extends('layouts.app')

@section('title', 'Log a sale')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-6">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <div>
                <h1 class="text-lg font-bold">Log a sale</h1>
                @if ($session?->activated_at)
                    <p class="text-xs text-gray-500">Session since {{ $session->activated_at->format('g:i A') }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if ($session?->is_active)
                    <a href="{{ route('sales.close') }}" data-offline-disable class="text-sm font-medium text-red-600">
                        Close day
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500">Sign out</button>
                </form>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('status'))
            <div class="mt-3 rounded border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mt-3 rounded border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Sale form --}}
        <form method="POST" action="{{ route('sales.store') }}" id="sale-form" class="mt-4 flex flex-col gap-4">
            @csrf

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Item</span>
                <select
                    name="item_id"
                    required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
                >
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                            {{ $item->name }} — ETB {{ number_format($item->sale_price, 2) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Quantity</span>
                @include('sales._stepper')
            </div>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Payment method</span>
                <select
                    name="payment_method_id"
                    required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
                >
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                            {{ $method->name }}
                        </option>
                    @endforeach
                </select>
            </label>
        </form>

        {{-- Today's sales --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 font-semibold">Today's sales</h2>

        <ul class="mt-3 flex flex-col divide-y divide-gray-100">
            @forelse ($todaySales as $sale)
                <li class="flex items-start justify-between gap-3 py-3">
                    <div class="min-w-0">
                        <p class="font-medium">{{ $sale->item->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $sale->quantity }} × ETB {{ number_format($sale->unit_price, 2) }} · {{ $sale->paymentMethod->name }}
                        </p>
                        <p class="text-sm font-semibold">ETB {{ number_format($sale->quantity * $sale->unit_price, 2) }}</p>
                    </div>
                    @if ($session?->is_active)
                        <div class="flex shrink-0 gap-2">
                            <a href="{{ route('sales.edit', $sale) }}" class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 active:bg-gray-100">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded border border-red-200 px-3 py-1.5 text-sm text-red-600 active:bg-red-50">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </li>
            @empty
                <li class="py-6 text-center text-sm text-gray-400">No sales yet today.</li>
            @endforelse
        </ul>
    </div>
@endsection

@section('actions')
    <button
        type="submit"
        form="sale-form"
        data-offline-disable
        class="w-full rounded bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Log sale
    </button>
@endsection
