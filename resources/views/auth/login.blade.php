@extends('layouts.app')

@section('title', 'Sign in')
@section('page-title', 'Filseta')

@section('content')
    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" id="login-form">
        @csrf
        <div class="field">
            <label>Email address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="form-input">
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required autocomplete="current-password" class="form-input">
        </div>
        <label style="display:flex;align-items:center;gap:8px;font-size:0.875rem;color:#64748b;margin-top:4px;">
            <input type="checkbox" name="remember" value="1" style="width:18px;height:18px;">
            Remember me
        </label>
    </form>
@endsection

@section('bottom-bar')
    <button type="submit" form="login-form" class="btn btn-indigo">Sign in</button>
@endsection
