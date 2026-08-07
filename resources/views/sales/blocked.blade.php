@extends('layouts.app')

@section('title', 'Day closed')

@php
    $colors = ['#4f46e5', '#f59e0b', '#10b981', '#ef4444', '#3b82f6', '#ec4899'];
    $closed = $session !== null && ! $session->is_active;
@endphp

@section('content')
    @if ($closed)
        <div class="relative mx-auto flex min-h-dvh max-w-md flex-col items-center justify-center px-4 py-12 text-center">
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                @for ($i = 0; $i < 16; $i++)
                    <span
                        class="confetti-piece"
                        style="left: {{ rand(2, 94) }}%; background: {{ $colors[$i % count($colors)] }}; animation-delay: {{ rand(0, 25) / 10 }}s; animation-duration: {{ rand(30, 45) / 10 }}s;"
                    ></span>
                @endfor
            </div>

            <span class="animate-bounce text-7xl">&#127881;</span>

            <h1 class="pop-in mt-6 bg-gradient-to-r from-indigo-600 via-violet-600 to-green-600 bg-clip-text text-5xl font-extrabold text-transparent">
                {{ auth()->user()->name }}
            </h1>

            <p class="mt-4 text-lg text-gray-700">Great work today! Your day is officially closed.</p>

            <div class="mt-8 grid w-full grid-cols-2 gap-3">
                <div class="pop-in rounded-2xl border border-gray-200 bg-white p-4 shadow-sm" style="animation-delay: 0.15s">
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats->count }}</p>
                    <p class="mt-1 text-sm text-gray-500">sales today</p>
                </div>
                <div class="pop-in rounded-2xl border border-gray-200 bg-white p-4 shadow-sm" style="animation-delay: 0.3s">
                    <p class="text-3xl font-bold text-green-600">ETB {{ number_format((float) $stats->revenue, 2) }}</p>
                    <p class="mt-1 text-sm text-gray-500">revenue today</p>
                </div>
            </div>

            <p class="mt-8 text-sm text-gray-500">
                See you tomorrow! Need to resume? Ask the admin to reactivate you.
            </p>
        </div>
    @else
        <div class="mx-auto flex min-h-dvh max-w-md flex-col px-4 pt-12">
            <h1 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-gray-600">
                Your daily session has not been activated yet. Please ask the admin to activate you for today
                before you can start recording sales.
            </p>
        </div>
    @endif
@endsection

@section('actions')
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
            Sign out
        </button>
    </form>
@endsection
