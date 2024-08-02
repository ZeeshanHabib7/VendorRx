<?php

use App\Http\Controllers\LoginRegisterControllers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users/forget-password', [LoginRegisterControllers::class, 'forgetPassword']);
Route::post('/users/send-email', [LoginRegisterControllers::class, 'sendEmail'])->name('sendEmail');
Route::get('/users/reset-password/{id}', [LoginRegisterControllers::class, 'resetPasswordPageLoad']);
Route::post('/users/reset-password', [LoginRegisterControllers::class, 'resetPassword'])->name('resetPassword');