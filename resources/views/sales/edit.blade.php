@extends('layouts.app')

@section('title', 'Edit sale')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-12">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">Edit sale</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $sale->item->name }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 active:text-gray-700">Sign out</button>
            </form>
        </div>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('sales.update', $sale) }}" id="edit-sale-form" class="mt-6 rounded-2xl border border-gray-200 bg-white p-4">
            @csrf
            @method('PUT')

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Item</span>
                <select disabled class="rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-gray-500">
                    <option>{{ $sale->item->name }} &mdash; ETB {{ number_format($sale->item->sale_price, 2) }}</option>
                </select>
                <span class="text-xs text-gray-500">Item cannot be changed; only quantity and payment method.</span>
            </label>

            <label class="mt-4 flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Quantity</span>
                @include('sales._stepper', ['quantity' => $sale->quantity])
            </label>

            <label class="mt-4 flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Payment method</span>
                <select
                    name="payment_method_id"
                    required
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}" @selected($sale->payment_method_id == $method->id)>
                            {{ $method->name }}
                        </option>
                    @endforeach
                </select>
            </label>
        </form>
    </div>
@endsection

@section('actions')
    <a href="{{ route('sales.index') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
        Cancel
    </a>
    <button
        type="submit"
        form="edit-sale-form"
        class="flex-1 rounded-xl bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Update sale
    </button>
@endsection
