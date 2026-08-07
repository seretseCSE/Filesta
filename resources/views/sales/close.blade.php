@extends('layouts.app')

@section('title', 'Close day')

@section('content')
    <div class="mx-auto max-w-md px-4 pt-12">
        <h1 class="text-2xl font-bold">Close day</h1>
        <p class="mt-2 text-gray-600">
            Review your performance before closing out for the day. Once closed, you cannot log or edit sales.
        </p>

        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <dl class="flex flex-col gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Today's revenue</dt>
                    <dd class="mt-1 text-3xl font-bold tracking-tight text-gray-900">
                        ETB {{ number_format($todayRevenue, 2) }}
                    </dd>
                </div>
                
                <div class="border-t border-gray-100 pt-6">
                    <dt class="text-sm font-medium text-gray-500">Cumulative event revenue</dt>
                    <dd class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        ETB {{ number_format($cumulativeRevenue, 2) }}
                    </dd>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <dt class="text-sm font-medium text-gray-500">Current rank</dt>
                    <dd class="mt-1 flex items-baseline gap-2">
                        <span class="text-2xl font-bold tracking-tight text-gray-900">#{{ $rank }}</span>
                        <span class="text-sm text-gray-500">among all salesmen</span>
                    </dd>
                </div>
            </dl>
        </div>

        <form method="POST" action="{{ route('sales.close.store') }}" id="close-form" class="mt-8">
            @csrf
        </form>
    </div>
@endsection

@section('actions')
    <a href="{{ route('sales.index') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-base font-semibold text-gray-700 active:bg-gray-100">
        Cancel
    </a>
    <button
        type="submit"
        form="close-form"
        class="flex-1 rounded-xl bg-red-600 px-4 py-3 text-base font-semibold text-white active:bg-red-700"
    >
        Confirm close out
    </button>
@endsection
