@extends('layouts.app')

@section('title', "You're closed out")
@section('page-title', "You're closed out")

@php
    $colors = ['#4f46e5', '#f59e0b', '#10b981', '#ef4444', '#3b82f6', '#ec4899'];
@endphp

@section('content')
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

        <p class="pop-in mt-6 text-sm font-bold uppercase tracking-widest text-indigo-600">Congratulations</p>

        <h1 class="pop-in mt-1 bg-gradient-to-r from-indigo-600 via-violet-600 to-green-600 bg-clip-text text-5xl font-extrabold text-transparent">
            {{ auth()->user()->name }}
        </h1>

        <p class="mt-4 text-lg text-gray-700">You've officially closed out your sales day. Great work today!</p>

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
            See you tomorrow! Ask the admin to reactivate you to start a new day.
        </p>
    </div>
@endsection

@section('actions')
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="btn btn-outline w-full">
            Sign out
        </button>
    </form>
@endsection
