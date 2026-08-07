<?php

namespace App\Http\Controllers;

use App\Models\DailySession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $session = DailySession::query()
            ->where('user_id', $request->user()->id)
            ->where('date', today()->toDateString())
            ->first();

        return view('sales.index', ['session' => $session]);
    }

    public function blocked(): View
    {
        return view('sales.blocked');
    }
}
