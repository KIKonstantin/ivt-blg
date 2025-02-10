<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/iviblg-admin/login', function () {
        return view('admin.login');
    })->name('admin.login');

    Route::post('/iviblg-admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
});


Route::post('/iviblg-admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
