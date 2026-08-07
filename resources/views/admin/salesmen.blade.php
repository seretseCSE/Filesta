@extends('layouts.app')

@section('title', 'Salesmen')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-12">
        <h1 class="text-2xl font-bold">Salesmen</h1>

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

        <form method="POST" action="{{ route('admin.salesmen.store') }}" id="add-salesman-form" class="mt-6 rounded-2xl border border-gray-200 bg-white p-4">
            @csrf
            <h2 class="font-semibold">Add salesman</h2>
            <div class="mt-3 flex flex-col gap-3">
                <input
                    type="text"
                    name="name"
                    placeholder="Full name"
                    value="{{ old('name') }}"
                    required
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                <input
                    type="tel"
                    name="phone"
                    placeholder="Phone number"
                    value="{{ old('phone') }}"
                    required
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
                <input
                    type="password"
                    name="pin"
                    placeholder="PIN (at least 4 characters)"
                    required
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
            </div>
        </form>

        <h2 class="mt-8 font-semibold">Today's roster</h2>

        <ul class="mt-3 flex flex-col gap-3">
            @forelse ($salesmen as $salesman)
                @php $active = $salesman->today_session?->is_active ?? false; @endphp
                <li class="rounded-2xl border border-gray-200 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold">
                                {{ $salesman->name }}
                                <span class="ml-1 inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                    #{{ $salesman->rank }}
                                </span>
                            </p>
                            <p class="mt-0.5 text-sm text-gray-600">{{ $salesman->phone }}</p>
                            <p class="mt-1 text-sm text-gray-800">
                                Revenue: <span class="font-semibold">ETB {{ number_format($salesman->revenue, 2) }}</span>
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-2">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $active ? 'Active today' : 'Not active' }}
                            </span>
                            @if ($active)
                                <form method="POST" action="{{ route('admin.salesmen.deactivate', $salesman) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-700 active:bg-gray-100">
                                        Deactivate
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.salesmen.activate', $salesman) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white active:bg-indigo-700">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </li>
            @empty
                <li class="rounded-2xl border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500">
                    No salesmen yet.
                </li>
            @endforelse
        </ul>
    </div>
@endsection

@section('actions')
    <button
        type="submit"
        form="add-salesman-form"
        class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Add salesman
    </button>
@endsection
