@extends('layouts.app')

@section('title', 'Items')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-6">

        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-lg font-bold">Items</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600">← Dashboard</a>
        </div>

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

        {{-- Add item form --}}
        <form method="POST" action="{{ route('admin.items.store') }}" id="add-item-form" class="mt-4 flex flex-col gap-3">
            @csrf
            <h2 class="text-sm font-semibold text-gray-700">Add item</h2>
            <input type="text" name="name" placeholder="Item name" value="{{ old('name') }}" required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            <input type="number" name="cost_price" step="0.01" min="0" placeholder="Cost price (ETB)" value="{{ old('cost_price') }}" required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            <input type="number" name="sale_price" step="0.01" min="0" placeholder="Sale price (ETB)" value="{{ old('sale_price') }}" required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            <input type="number" name="stock_quantity" min="0" placeholder="Initial stock" value="{{ old('stock_quantity') }}" required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
            <input type="number" name="low_stock_threshold" min="0" placeholder="Low-stock threshold" value="{{ old('low_stock_threshold') }}" required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none">
        </form>

        {{-- Item list --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 text-sm font-semibold text-gray-700">All items</h2>

        <ul class="mt-3 flex flex-col divide-y divide-gray-100">
            @forelse ($items as $item)
                @php $low = $item->stock_quantity <= $item->low_stock_threshold; @endphp
                <li class="py-3 {{ $low ? 'text-red-700' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium">
                                {{ $item->name }}
                                @if ($low)
                                    <span class="ml-1 text-xs font-semibold text-red-600">Low stock</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-500">
                                Cost: ETB {{ number_format($item->cost_price, 2) }} · Sale: ETB {{ number_format($item->sale_price, 2) }}
                            </p>
                            <p class="text-sm {{ $low ? 'font-semibold text-red-600' : 'text-gray-700' }}">
                                Stock: {{ $item->stock_quantity }} (threshold: {{ $item->low_stock_threshold }})
                            </p>
                            <form method="POST" action="{{ route('admin.items.restock', $item) }}" class="mt-2 flex gap-2">
                                @csrf
                                <input type="number" name="quantity" min="1" placeholder="Qty" required
                                    class="w-20 rounded border border-gray-300 px-2 py-2 text-base focus:border-indigo-500 focus:outline-none">
                                <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white active:bg-indigo-700">
                                    Restock
                                </button>
                            </form>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-2">
                            <a href="{{ route('admin.items.edit', $item) }}" class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 active:bg-gray-100">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.items.destroy', $item) }}" onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 active:text-red-800">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-gray-400">No items yet.</li>
            @endforelse
        </ul>
    </div>
@endsection

@section('actions')
    <button
        type="submit"
        form="add-item-form"
        class="w-full rounded bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Add item
    </button>
@endsection
