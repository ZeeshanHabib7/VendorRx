<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController_SA;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginRegisterControllers;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




// Route::get('/products', [ProductController::class, 'index'])->middleware('auth:api');




Route::post('/users/register', [LoginRegisterControllers::class, 'register']);
Route::post('/users/login', [LoginRegisterControllers::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/products', [ProductsController_SA::class, 'getData']);
});




