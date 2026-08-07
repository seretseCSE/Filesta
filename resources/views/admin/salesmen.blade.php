@extends('layouts.app')

@section('title', 'Salesmen')
@section('page-title', 'Salesmen')

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
            <div class="relative">
                <input
                    type="password"
                    name="password"
                    id="add-salesman-password"
                    placeholder="Password"
                    required
                    class="w-full rounded border border-gray-300 px-3 py-3 pr-10 text-base focus:border-indigo-500 focus:outline-none"
                >
                <button type="button" data-eye="add-salesman-password" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400" aria-label="Show password">
                    <svg data-eye-open class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg data-eye-off class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
            </div>
        </form>

        {{-- Roster --}}
        <h2 class="mt-6 border-t border-gray-200 pt-4 text-sm font-semibold text-gray-700">Today's roster</h2>

        <ul class="mt-3 flex flex-col divide-y divide-gray-100">
            @forelse ($salesmen as $salesman)
                @php $active = $salesman->today_session?->is_active ?? false; @endphp
                <li class="py-3">
                    <div class="flex items-center justify-between gap-3">
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
                            <button type="button" data-pw-toggle="password-form-{{ $salesman->id }}" class="text-sm text-gray-500 active:text-gray-700">
                                Reset password
                            </button>
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.salesmen.password', $salesman) }}"
                        id="password-form-{{ $salesman->id }}"
                        class="mt-3 hidden flex-col gap-2 rounded border border-gray-200 bg-gray-50 p-3"
                    >
                        @csrf
                        <p class="text-sm font-medium text-gray-700">New password for {{ $salesman->name }}</p>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password-{{ $salesman->id }}"
                                placeholder="New password"
                                required
                                minlength="4"
                                class="w-full rounded border border-gray-300 px-3 py-2 pr-10 text-base focus:border-indigo-500 focus:outline-none"
                            >
                            <button type="button" data-eye="password-{{ $salesman->id }}" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400" aria-label="Show password">
                                <svg data-eye-open class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg data-eye-off class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white active:bg-indigo-700">
                            Update password
                        </button>
                    </form>
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

@push('scripts')
    <script>
        document.querySelectorAll('[data-pw-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById(btn.dataset.pwToggle).classList.toggle('hidden');
            });
        });

        document.querySelectorAll('[data-eye]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.eye);
                input.type = input.type === 'password' ? 'text' : 'password';
                btn.querySelector('[data-eye-open]').classList.toggle('hidden', input.type !== 'password');
                btn.querySelector('[data-eye-off]').classList.toggle('hidden', input.type === 'password');
            });
        });
    </script>
@endpush
