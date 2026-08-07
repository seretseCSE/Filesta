@extends('layouts.app')

@section('title', 'Log a sale')
@section('page-title', 'Log a sale')

@section('app-bar-actions')
    @if ($session?->activated_at)
        <span style="font-size:0.75rem;color:#94a3b8;">Since {{ $session->activated_at->format('g:i A') }}</span>
    @endif
    @if ($session?->is_active)
        <a href="{{ route('sales.close') }}" data-offline-disable style="color:#f87171;font-size:0.875rem;font-weight:600;background:none;border:none;cursor:pointer;text-decoration:none;">Close day</a>
    @endif
@endsection

@section('content')
    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('sales.store') }}" id="sale-form">
        @csrf

        <div class="field">
            <label class="field-label">Item</label>
            <select name="item_id" required class="form-select">
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                        {{ $item->name }} — ETB {{ number_format($item->sale_price, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label class="field-label">Quantity</label>
            @include('sales._stepper')
        </div>

        <div class="field" style="margin-bottom:0;">
            <label class="field-label">Payment method</label>
            <select name="payment_method_id" required class="form-select">
                @foreach ($paymentMethods as $method)
                    <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>
                        {{ $method->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <p class="section-title">Today's sales</p>

    @forelse ($todaySales as $sale)
        <div class="list-row">
            <div style="min-width:0;">
                <div class="list-row-title">{{ $sale->item->name }}</div>
                <div class="list-row-sub">{{ $sale->quantity }} × ETB {{ number_format($sale->unit_price, 2) }} · {{ $sale->paymentMethod->name }}</div>
                <div class="list-row-amount">ETB {{ number_format($sale->quantity * $sale->unit_price, 2) }}</div>
            </div>
            @if ($session?->is_active)
                <div class="list-actions">
                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale?')" style="margin:0;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ghost-red">Delete</button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <p style="text-align:center;color:#94a3b8;padding:32px 0;font-size:0.875rem;">No sales yet today.</p>
    @endforelse
@endsection

@section('bottom-bar')
    <button type="submit" form="sale-form" data-offline-disable class="btn btn-indigo">Log sale</button>
@endsection
