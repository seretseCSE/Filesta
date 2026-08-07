@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mx-auto flex min-h-dvh max-w-md flex-col px-4 pt-12">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Signed in as {{ auth()->user()->name }}.</p>

        <a
            href="{{ route('admin.salesmen') }}"
            class="mt-6 rounded-2xl border border-gray-200 bg-white p-4 font-semibold text-indigo-600 active:bg-gray-50"
        >
            Manage salesmen &rsaquo;
        </a>
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
