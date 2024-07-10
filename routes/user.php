<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;


//-------- Auth Routes --------
Route::post('login', [AuthController::class,'login']);
Route::post('signup', [AuthController::class,'signUp']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('me', [AuthController::class,'me']); //user route
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']); //token refresh route
    
    Route::get('products',[ProductController::class,'index']);     
});

Route::get('all', [UserController::class, 'getAllUsers']);

?>