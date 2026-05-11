<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BankController as AdminBankController;
use App\Http\Controllers\Admin\GatewayController as AdminGatewayController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])->name('contact.store');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

Route::get('/cache-clear', function () {
    $secret = config('app.cache_clear_secret');
    if (! is_string($secret) || $secret === '') {
        abort(403);
    }
    $token = (string) request()->query('token', '');
    if (! hash_equals($secret, $token)) {
        abort(403);
    }
    Artisan::call('optimize:clear');

    return response('optimize:clear completed.', 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->middleware('throttle:10,1')->name('cache.clear');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::post('kyc/pan', [KycController::class, 'storePan'])->name('kyc.pan');

    Route::middleware('kyc.verified')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('banks', [BankController::class, 'index'])->name('banks.index');
        Route::post('banks', [BankController::class, 'store'])->name('banks.store');
        Route::patch('banks/{bank}', [BankController::class, 'update'])->name('banks.update');
        Route::delete('banks/{bank}', [BankController::class, 'destroy'])->name('banks.destroy');

        Route::get('payments', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');

        Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
        Route::patch('wallet', [WalletController::class, 'update'])->name('wallet.update');
    });

    Route::middleware('admin')->group(function () {
        Route::get('admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
        Route::patch('admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');

        Route::get('admin/banks', [AdminBankController::class, 'index'])->name('admin.banks.index');
        Route::get('admin/banks/{bank}/edit', [AdminBankController::class, 'edit'])->name('admin.banks.edit');
        Route::patch('admin/banks/{bank}', [AdminBankController::class, 'update'])->name('admin.banks.update');

        Route::get('admin/gateways', [AdminGatewayController::class, 'index'])->name('admin.gateways.index');
        Route::get('admin/gateways/create', [AdminGatewayController::class, 'create'])->name('admin.gateways.create');
        Route::post('admin/gateways', [AdminGatewayController::class, 'store'])->name('admin.gateways.store');
        Route::get('admin/gateways/{gateway}/edit', [AdminGatewayController::class, 'edit'])->name('admin.gateways.edit');
        Route::patch('admin/gateways/{gateway}', [AdminGatewayController::class, 'update'])->name('admin.gateways.update');
        Route::delete('admin/gateways/{gateway}', [AdminGatewayController::class, 'destroy'])->name('admin.gateways.destroy');
    });
});
