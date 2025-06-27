<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('/iveta-adm/login', function () {
        return view('admin.login');
    })->name('admin.login');

    Route::post('/iveta-adm/login', [AuthController::class, 'login'])->name('admin.login.submit');
});


Route::post('/iveta-adm/logout', [AuthController::class, 'logout'])->name('admin.logout');
