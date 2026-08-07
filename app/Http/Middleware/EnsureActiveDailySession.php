<?php

namespace App\Http\Middleware;

use App\Models\DailySession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveDailySession
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasActiveSession = DailySession::query()
            ->where('user_id', $request->user()->id)
            ->where('date', today()->toDateString())
            ->where('is_active', true)
            ->exists();

        if (! $hasActiveSession) {
            return redirect()->route('sales.blocked');
        }

        return $next($request);
    }
}
