@extends('layouts.app')

@section('title', 'No active session')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-12">
        <h1 class="text-lg font-bold">No active session</h1>
        <p class="mt-2 text-sm text-gray-500">Your session for today hasn't been activated yet. Ask the admin to activate you before you can record sales.</p>
    </div>
@endsection

@section('actions')
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="w-full rounded border border-gray-300 bg-white px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
            Sign out
        </button>
    </form>
@endsection
