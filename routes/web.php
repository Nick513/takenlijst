<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Modal\ModalController;
use App\Http\Controllers\Language\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication
Auth::routes();
Route::get('/activate/{code}', [RegisterController::class, 'activate'])->name('activate');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Settings
Route::middleware('auth')->get('/settings', [SettingsController::class, 'settings'])->name('settings');

// Modal
Route::get('/modal', [ModalController::class, 'load'])->name('load');

// Language
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('switch_language');
