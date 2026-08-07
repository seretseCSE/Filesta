@extends('layouts.app')

@section('title', 'Sales')

@section('content')
    <div class="mx-auto flex min-h-dvh max-w-md flex-col px-4 pt-12">
        <h1 class="text-2xl font-bold">Sales</h1>
        <p class="mt-2 text-gray-600">
            Session active since {{ $session?->activated_at?->format('g:i A') }}.
        </p>
    </div>
@endsection

@section('actions')
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
            Sign out
        </button>
    </form>
@endsection
