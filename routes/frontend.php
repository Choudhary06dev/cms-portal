<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController as FrontendHomeController;
use App\Http\Controllers\Frontend\AuthController as FrontendAuthController;

Route::get('/', [FrontendHomeController::class, 'index'])->name('frontend.home');
Route::get('/about', [FrontendHomeController::class, 'about'])->name('frontend.about');
Route::get('/contact', [FrontendHomeController::class, 'contact'])->name('frontend.contact');
Route::get('/login-public', [FrontendAuthController::class, 'showLogin'])->name('frontend.login');
Route::get('/register-public', [FrontendAuthController::class, 'showRegister'])->name('frontend.register');
Route::post('/login-public', [FrontendAuthController::class, 'login'])->name('frontend.login.post');
Route::post('/logout-public', [FrontendAuthController::class, 'logout'])->name('frontend.logout');
Route::post('/register-public', [FrontendAuthController::class, 'register'])->name('frontend.register.post');


