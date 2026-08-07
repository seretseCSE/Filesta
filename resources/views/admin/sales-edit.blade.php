@extends('layouts.app')

@section('title', 'Edit sale')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-6">
        <h1 class="text-lg font-bold">Edit sale</h1>
        <p class="text-sm text-gray-500">
            {{ $sale->item->name }} · {{ $sale->user->name }} · {{ $sale->sale_date->format('M j, Y') }}
        </p>

        @if ($errors->any())
            <div class="mt-3 rounded border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sales.update', $sale) }}" id="edit-sale-form" class="mt-4 flex flex-col gap-4">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Quantity</span>
                @include('sales._stepper', ['quantity' => $sale->quantity])
            </div>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Payment method</span>
                <select
                    name="payment_method_id"
                    required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
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
    <a href="{{ route('admin.sales') }}" class="rounded border border-gray-300 px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
        Cancel
    </a>
    <button
        type="submit"
        form="edit-sale-form"
        class="flex-1 rounded bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Update sale
    </button>
@endsection
