@extends('layouts.app')

@section('title', 'Log a sale')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-12">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">Log a sale</h1>
                @if ($session?->activated_at)
                    <p class="mt-1 text-sm text-gray-600">Session active since {{ $session->activated_at->format('g:i A') }}.</p>
                @endif
            </div>
            <div class="flex flex-col items-end gap-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 active:text-gray-700">Sign out</button>
                </form>
                @if ($session?->is_active)
                    <a href="{{ route('sales.close') }}" data-offline-disable class="text-sm font-semibold text-red-600 active:text-red-700">
                        Close day
                    </a>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-xl bg-green-50 p-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('sales.store') }}" id="sale-form" class="mt-6 rounded-2xl border border-gray-200 bg-white p-4">
            @csrf

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Item</span>
                <select
                    name="item_id"
                    required
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                            {{ $item->name }} &mdash; ETB {{ number_format($item->sale_price, 2) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="mt-4 flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Quantity</span>
                @include('sales._stepper')
            </label>

            <label class="mt-4 flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Payment method</span>
                <select
                    name="payment_method_id"
                    required
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                            {{ $method->name }}
                        </option>
                    @endforeach
                </select>
            </label>
        </form>

        <h2 class="mt-8 font-semibold">Today's sales</h2>

        <ul class="mt-3 flex flex-col gap-3">
            @forelse ($todaySales as $sale)
                <li class="rounded-2xl border border-gray-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold">{{ $sale->item->name }}</p>
                            <p class="mt-0.5 text-sm text-gray-600">
                                {{ $sale->quantity }} &times; ETB {{ number_format($sale->unit_price, 2) }} &middot; {{ $sale->paymentMethod->name }}
                            </p>
                            <p class="mt-1 text-sm font-semibold text-gray-800">
                                ETB {{ number_format($sale->quantity * $sale->unit_price, 2) }}
                            </p>
                        </div>
                        @if ($session?->is_active)
                            <div class="flex shrink-0 flex-col items-end gap-2">
                                <a
                                    href="{{ route('sales.edit', $sale) }}"
                                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 active:bg-gray-100"
                                >
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale? Stock will be restored.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-600 active:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </li>
            @empty
                <li class="rounded-2xl border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">
                    No sales yet today.
                </li>
            @endforelse
        </ul>
    </div>
@endsection

@section('actions')
    <button
        type="submit"
        form="sale-form"
        data-offline-disable
        class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700 transition-colors"
    >
        Log sale
    </button>
@endsection
