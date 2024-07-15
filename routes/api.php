<?php

use App\Http\Controllers\PermissionController_SA;
use App\Http\Controllers\RolesController_SA;
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




// Route::get('/products', [ProductController::class, 'index']);



Route::post('/users/register', [LoginRegisterControllers::class, 'register']);
Route::post('/users/login', [LoginRegisterControllers::class, 'login']);

//PROTECTED Routes
Route::middleware('AuthGuard')->group(function () {
    Route::get('/products/getFilterData', [ProductsController_SA::class, 'getData']);
    Route::apiResource('products', ProductsController_SA::class);
    Route::apiResource('permissions', PermissionController_SA::class);
    Route::apiResource('roles', RolesController_SA::class);
});









