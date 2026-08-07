@extends('layouts.app')

@section('title', 'Salesmen')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-6">

        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-lg font-bold">Salesmen</h1>
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

        {{-- Add salesman form --}}
        <form method="POST" action="{{ route('admin.salesmen.store') }}" id="add-salesman-form" class="mt-4 flex flex-col gap-3">
            @csrf
            <h2 class="text-sm font-semibold text-gray-700">Add salesman</h2>
            <input
                type="text"
                name="name"
                placeholder="Full name"
                value="{{ old('name') }}"
                required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
            >
            <input
                type="email"
                name="email"
                placeholder="Email address"
                value="{{ old('email') }}"
                required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
            >
            <input
                type="password"
                name="password"
                placeholder="Password"
                required
                class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
            >
        </form>

        {{-- Roster --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 text-sm font-semibold text-gray-700">Today's roster</h2>

        <ul class="mt-3 flex flex-col divide-y divide-gray-100">
            @forelse ($salesmen as $salesman)
                @php $active = $salesman->today_session?->is_active ?? false; @endphp
                <li class="flex items-center justify-between gap-3 py-3">
                    <div class="min-w-0">
                        <p class="font-medium">
                            {{ $salesman->name }}
                            <span class="ml-1 text-xs text-gray-400">#{{ $salesman->rank }}</span>
                        </p>
                        <p class="text-sm text-gray-500">{{ $salesman->email }}</p>
                        <p class="text-sm text-gray-700">ETB {{ number_format($salesman->revenue, 2) }}</p>
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        <span class="text-xs font-medium {{ $active ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $active ? 'Active' : 'Inactive' }}
                        </span>
                        @if ($active)
                            <form method="POST" action="{{ route('admin.salesmen.deactivate', $salesman) }}">
                                @csrf
                                <button type="submit" class="rounded border border-gray-300 px-3 py-1.5 text-sm text-gray-700 active:bg-gray-100">
                                    Deactivate
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.salesmen.activate', $salesman) }}">
                                @csrf
                                <button type="submit" class="rounded bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white active:bg-indigo-700">
                                    Activate
                                </button>
                            </form>
                        @endif
                    </div>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-gray-400">No salesmen yet.</li>
            @endforelse
        </ul>
    </div>
@endsection

@section('actions')
    <button
        type="submit"
        form="add-salesman-form"
        class="w-full rounded bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Add salesman
    </button>
@endsection
