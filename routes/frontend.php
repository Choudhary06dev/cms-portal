<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController as FrontendHomeController;
use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;

Route::get('/', [FrontendHomeController::class, 'index'])->name('frontend.home');
Route::get('/features', [FrontendHomeController::class, 'features'])->name('frontend.features');
Route::get('/dashboard', [FrontendHomeController::class, 'dashboard'])->middleware('auth:frontend')->name('frontend.dashboard');
Route::get('/login', [FrontendAuthController::class, 'showLogin'])->name('frontend.login');
Route::get('/register', [FrontendAuthController::class, 'showRegister'])->name('frontend.register');
Route::post('/login', [FrontendAuthController::class, 'login'])->name('frontend.login.post');
Route::post('/logout', [FrontendAuthController::class, 'logout'])->name('frontend.logout');
Route::post('/register', [FrontendAuthController::class, 'register'])->name('frontend.register.post');



