@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <div class="mx-auto flex min-h-dvh max-w-md flex-col px-4 pt-12">
        <h1 class="text-2xl font-bold">Sign in</h1>
        <p class="mt-2 text-gray-600">Admins sign in with email and password. Salesmen sign in with phone and PIN.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" id="login-form" class="mt-6 flex flex-col gap-4">
            @csrf

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Email or phone</span>
                <input
                    type="text"
                    name="identity"
                    value="{{ old('identity') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Password or PIN</span>
                <input
                    type="password"
                    name="secret"
                    required
                    autocomplete="current-password"
                    class="rounded-xl border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                >
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300">
                Remember me
            </label>
        </form>
    </div>
@endsection

@section('actions')
    <button
        type="submit"
        form="login-form"
        class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Sign in
    </button>
@endsection
