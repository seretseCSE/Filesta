@extends('layouts.app')

@section('title', 'Close day')
@section('page-title', 'Close day')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-6">
        <h1 class="text-lg font-bold">Close day</h1>
        <p class="mt-1 text-sm text-gray-500">Once closed, you cannot log or edit sales for today.</p>

        <div class="mt-6 flex flex-col divide-y divide-gray-100 rounded border border-gray-200">
            <div class="px-4 py-4">
                <p class="text-sm text-gray-500">Today's revenue</p>
                <p class="mt-1 text-2xl font-bold">ETB {{ number_format($todayRevenue, 2) }}</p>
            </div>
            <div class="px-4 py-4">
                <p class="text-sm text-gray-500">Cumulative event revenue</p>
                <p class="mt-1 text-xl font-bold">ETB {{ number_format($cumulativeRevenue, 2) }}</p>
            </div>
            <div class="px-4 py-4">
                <p class="text-sm text-gray-500">Your rank</p>
                <p class="mt-1 text-xl font-bold">#{{ $rank }} among all salesmen</p>
            </div>
        </div>

        <form method="POST" action="{{ route('sales.close.store') }}" id="close-form">
            @csrf
        </form>
    </div>
@endsection

@section('actions')
    <a href="{{ route('sales.index') }}" class="rounded border border-gray-300 px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
        Cancel
    </a>
    <button
        type="submit"
        form="close-form"
        data-offline-disable
        class="flex-1 rounded bg-red-600 px-4 py-3 text-base font-semibold text-white active:bg-red-700"
    >
        Confirm close out
    </button>
@endsection
