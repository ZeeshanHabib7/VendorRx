<?php


use App\Http\Controllers\AddToCartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController_SA;
use App\Http\Controllers\RolesController_SA;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductsController_SA;
use App\Http\Controllers\LoginRegisterControllers;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;



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
Route::post('/users/send-email', [LoginRegisterControllers::class, 'sendEmail'])->name('sendEmail');
Route::post('/users/reset-password', [LoginRegisterControllers::class, 'resetPassword'])->name('resetPassword');

//PROTECTED ROUTES

// FOR Admins and Users both
Route::middleware(['AuthGuard', 'RestrictTo:user,admin'])->group(function () {
    Route::get('/products/get-filter-data', [ProductsController_SA::class, 'getData']);
    Route::get('/order/get-order-history', [OrderController::class, 'getOrderHistory']);

});

//FOR Users Only
Route::middleware(['AuthGuard', 'RestrictTo:user', 'CheckEncryption'])->group(function () {
    Route::post('/products/add-to-cart', [AddToCartController::class, 'store']);
    Route::get('/order/cancel-order/{orderId}', [OrderController::class, 'cancelOrder']);

});

//FOR Admins Only
Route::middleware(['AuthGuard', 'RestrictTo:admin'])->group(function () {

    Route::post('/products', [ProductsController_SA::class, 'store'])->middleware('PermissionCheck:product.store');
    Route::get('/products/{id}', [ProductsController_SA::class, 'show'])->middleware('PermissionCheck:product.view');
    Route::put('/products/{id}', [ProductsController_SA::class, 'update'])->middleware('PermissionCheck:product.update');
    Route::delete('/products/{id}', [ProductsController_SA::class, ''])->middleware('PermissionCheck:product.delete');
    Route::post('/products/restore/{id}', [ProductsController_SA::class, 'restore'])->middleware('PermissionCheck:product.restore');
    Route::delete('/products/permanent-delete/{id}', [ProductsController_SA::class, 'permanentDelete'])->middleware('PermissionCheck:product.permanentDelete');


    Route::apiResource('permissions', PermissionController_SA::class);
    Route::apiResource('roles', RolesController_SA::class);

});


//Routes only made for checking the encrypted decrypted data
Route::post('/encrypt', function (Request $request) {
    return encrypt_payload($request);
});
Route::post('/decrypt', function (Request $request) {

    return decrypt_payload($request);
});

Route::get('all', [UserController::class, 'getAllUsers']);

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index'])->name('brands.index');
    Route::post('/', [BrandController::class, 'store'])->name('brands.store');
    Route::get('{id}', [BrandController::class, 'show'])->name('brands.show');
    Route::put('{id}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});







