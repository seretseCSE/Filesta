@extends('layouts.app')

@section('title', 'Edit item')
@section('page-title', 'Edit item')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-6">
        <h1 class="text-lg font-bold">Edit item</h1>
        <p class="text-sm text-gray-500">{{ $item->name }}</p>

        @if ($errors->any())
            <div class="mt-3 rounded border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.items.update', $item) }}" id="edit-item-form" class="mt-4 flex flex-col gap-4">
            @csrf
            @method('PUT')

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Name</span>
                <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Cost price (ETB)</span>
                <input type="number" name="cost_price" step="0.01" min="0" value="{{ old('cost_price', $item->cost_price) }}" required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Sale price (ETB)</span>
                <input type="number" name="sale_price" step="0.01" min="0" value="{{ old('sale_price', $item->sale_price) }}" required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Stock quantity</span>
                <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', $item->stock_quantity) }}" required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Low-stock threshold</span>
                <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $item->low_stock_threshold) }}" required
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            </label>
        </form>
    </div>
@endsection

@section('actions')
    <a href="{{ route('admin.items') }}" class="rounded border border-gray-300 px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
        Cancel
    </a>
    <button
        type="submit"
        form="edit-item-form"
        class="flex-1 rounded bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Save changes
    </button>
@endsection
