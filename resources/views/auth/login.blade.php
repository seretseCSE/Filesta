@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-12">
        <h1 class="text-xl font-bold">Filseta — Sign in</h1>

        @if ($errors->any())
            <div class="mt-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" id="login-form" class="mt-6 flex flex-col gap-4">
            @csrf

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Email address</span>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
                >
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700">Password</span>
                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded border border-gray-300 px-3 py-3 text-base focus:border-indigo-500 focus:outline-none"
                >
            </label>

            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" value="1" class="h-5 w-5 rounded border-gray-300">
                Remember me
            </label>
        </form>
    </div>
@endsection

@section('actions')
    <button
        type="submit"
        form="login-form"
        class="w-full rounded bg-indigo-600 px-4 py-3 text-base font-semibold text-white active:bg-indigo-700"
    >
        Sign in
    </button>
@endsection
