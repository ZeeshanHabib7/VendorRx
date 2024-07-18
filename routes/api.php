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

//PROTECTED Routes ---> For Authenticated Users Only
Route::get('/products/getFilterData', [ProductsController_SA::class, 'getData'])->middleware('AuthGuard');

//PROTECTED Routes ---> For Admins Only
Route::middleware(['AuthGuard', 'AdminCheck'])->group(function () {

    Route::post('/products', [ProductsController_SA::class, 'store'])->middleware('PermissionCheck:product.store');
    Route::get('/products/{id}', [ProductsController_SA::class, 'show'])->middleware('PermissionCheck:product.view');
    Route::put('/products/{id}', [ProductsController_SA::class, 'update'])->middleware('PermissionCheck:product.update');
    Route::delete('/products/{id}', [ProductsController_SA::class, ''])->middleware('PermissionCheck:product.delete');
    Route::post('/products/restore/{id}', [ProductsController_SA::class, 'restore'])->middleware('PermissionCheck:product.restore');
    Route::delete('/products/permanent-delete/{id}', [ProductsController_SA::class, 'permanentDelete'])->middleware('PermissionCheck:product.permanentDelete'); 


    Route::apiResource('permissions', PermissionController_SA::class);
    Route::apiResource('roles', RolesController_SA::class);
});









