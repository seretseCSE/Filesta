<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(
            auth()->user()->isAdmin() ? 'admin.dashboard' : 'sales.index'
        );
    }

    return redirect()->route('login');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

Route::prefix('sales')->middleware(['auth', 'role:salesman'])->group(function () {
    Route::get('/blocked', [SalesController::class, 'blocked'])->name('sales.blocked');
    Route::get('/', [SalesController::class, 'index'])->middleware('active.daily.session')->name('sales.index');
});
