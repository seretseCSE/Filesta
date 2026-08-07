@extends('layouts.app')

@section('title', 'Edit sale')
@section('page-title', 'Edit sale')

@section('app-bar-actions')
    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline" style="color:#94a3b8;border-color:#475569;">← Back</a>
@endsection

@section('content')
    <p style="color:#64748b;font-size:0.875rem;margin-bottom:20px;">{{ $sale->item->name }} — item cannot be changed.</p>

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('sales.update', $sale) }}" id="edit-sale-form">
        @csrf
        @method('PUT')

        <div class="field">
            <label class="field-label">Quantity</label>
            @include('sales._stepper', ['quantity' => $sale->quantity])
        </div>

        <div class="field" style="margin-bottom:0;">
            <label class="field-label">Payment method</label>
            <select name="payment_method_id" required class="form-select">
                @foreach ($paymentMethods as $method)
                    <option value="{{ $method->id }}" @selected($sale->payment_method_id == $method->id)>
                        {{ $method->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
@endsection

@section('bottom-bar')
    <a href="{{ route('sales.index') }}" class="btn btn-outline">Cancel</a>
    <button type="submit" form="edit-sale-form" class="btn btn-indigo">Update sale</button>
@endsection
