<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\DailySession;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    public function salesmen(): View
    {
        $salesmen = User::where('role', UserRole::Salesman)
            ->withAggregate('sales as revenue', 'SUM(quantity * unit_price)')
            ->get()
            ->each(fn (User $salesman) => $salesman->revenue = (float) ($salesman->revenue ?? 0))
            ->sortByDesc('revenue')
            ->values();

        $today = today()->toDateString();
        $todaySessions = DailySession::query()
            ->whereIn('user_id', $salesmen->pluck('id'))
            ->where('date', $today)
            ->get()
            ->keyBy('user_id');

        $salesmen->each(function (User $salesman, int $index) use ($todaySessions) {
            $salesman->rank = $index + 1;
            $salesman->today_session = $todaySessions->get($salesman->id);
        });

        return view('admin.salesmen', ['salesmen' => $salesmen]);
    }

    public function storeSalesman(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'pin' => ['required', 'string', 'min:4', 'max:10'],
        ]);

        User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'pin' => $data['pin'],
            'role' => UserRole::Salesman,
        ]);

        return redirect()->route('admin.salesmen')->with('status', 'Salesman added.');
    }

    public function activateSalesman(User $user): RedirectResponse
    {
        abort_unless($user->isSalesman(), 404);

        DailySession::updateOrCreate(
            ['user_id' => $user->id, 'date' => today()->toDateString()],
            ['activated_at' => now(), 'closed_at' => null, 'is_active' => true]
        );

        return back()->with('status', "{$user->name} is active for today.");
    }

    public function deactivateSalesman(User $user): RedirectResponse
    {
        abort_unless($user->isSalesman(), 404);

        DailySession::query()
            ->where('user_id', $user->id)
            ->where('date', today()->toDateString())
            ->update(['is_active' => false, 'closed_at' => now()]);

        return back()->with('status', "{$user->name} deactivated for today.");
    }
}
