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
            <div style="position:relative;">
                <input type="password" name="password" id="login-password" required autocomplete="current-password" class="form-input" style="padding-right:44px;">
                <button type="button" data-eye="login-password" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;padding:4px;" aria-label="Show password">
                    <svg data-eye-open class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg data-eye-off class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
            </div>
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

@push('scripts')
    <script>
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
