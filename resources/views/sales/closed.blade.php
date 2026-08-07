@extends('layouts.app')

@section('title', "You're closed out")

@section('content')
    <div class="mx-auto flex min-h-dvh max-w-md flex-col px-4 pt-12">
        <h1 class="text-2xl font-bold">You're closed out</h1>
        <p class="mt-2 text-gray-600">
            Great job today! Your sales session has been successfully closed.
            You can no longer log or edit sales for today.
        </p>
    </div>
@endsection

@section('actions')
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700">
            Sign out
        </button>
    </form>
@endsection
