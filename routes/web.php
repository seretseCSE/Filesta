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
    Route::get('/salesmen', [AdminController::class, 'salesmen'])->name('admin.salesmen');
    Route::post('/salesmen', [AdminController::class, 'storeSalesman'])->name('admin.salesmen.store');
    Route::post('/salesmen/{user}/activate', [AdminController::class, 'activateSalesman'])->name('admin.salesmen.activate');
    Route::post('/salesmen/{user}/deactivate', [AdminController::class, 'deactivateSalesman'])->name('admin.salesmen.deactivate');
});

Route::prefix('sales')->middleware(['auth', 'role:salesman'])->group(function () {
    Route::get('/blocked', [SalesController::class, 'blocked'])->name('sales.blocked');
    Route::get('/', [SalesController::class, 'index'])->middleware('active.daily.session')->name('sales.index');
    Route::post('/', [SalesController::class, 'store'])->middleware('active.daily.session')->name('sales.store');
    Route::get('/{sale}/edit', [SalesController::class, 'edit'])->middleware('active.daily.session')->name('sales.edit');
    Route::put('/{sale}', [SalesController::class, 'update'])->middleware('active.daily.session')->name('sales.update');
    Route::delete('/{sale}', [SalesController::class, 'destroy'])->middleware('active.daily.session')->name('sales.destroy');
});
