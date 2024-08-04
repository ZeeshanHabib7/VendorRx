<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\UserController_FH;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PermissionController_FH;
use App\Http\Controllers\RoleController_FH;


//-------- Auth Routes --------
Route::post('login', [AuthController::class,'login']);
Route::post('signup', [AuthController::class,'signUp']);

// Routes for authorized user
Route::middleware(['check.auth',])->group(function () {
    Route::post('me', [AuthController::class,'me']); //user route
    Route::post('logout', [AuthController::class,'logout']);
 
                        // Routes only accessed by super admin
    Route::group(['middleware' => ['role:superAdmin']], function () {

        //--------  Permission Crud routes -------- 
            Route::post('/permission', [PermissionController_FH::class, 'create']);
            Route::get('/permission', [PermissionController_FH::class, 'index']);
            Route::patch('/permission/{id}', [PermissionController_FH::class, 'edit']);
            Route::delete('/permission/{id}', [PermissionController_FH::class, 'destroy']);

        //--------  Role Crud route -------- 
            Route::post('/role', [RoleController_FH::class, 'create']);
            Route::get('/role', [RoleController_FH::class, 'index']);
            Route::patch('/role/{id}', [RoleController_FH::class, 'edit']);
            Route::delete('/role/{id}', [RoleController_FH::class, 'destroy']);

        //-------- Users create, update, delete Route --------
            Route::post('/create', [UserController_FH::class, 'create']);
            Route::put('/{id}', [UserController_FH::class, 'edit']);
            Route::delete('/{id}', [UserController_FH::class, 'destroy']);

    });

                        // Routes accessed by super admin and admin
    Route::group(['middleware' => ['role:superAdmin|admin']], function () {
        // Route to update permissions for a role (add or sync)
        Route::post('{role}/permissions', [RoleController_FH::class, 'updatePermissions']);

        //-------- Fetch All Users -------
        Route::get('get', [UserController_FH::class, 'index']);

        //-------- Fetch User by Id --------
        Route::get('get/{id}', [UserController_FH::class, 'show']);

        //-------- Product Create, update, delete Routes --------
        Route::post('product', [ProductController::class, 'create']);
        Route::put('product/{id}', [ProductController::class, 'edit']);
        Route::delete('product/{id}', [ProductController::class, 'destroy']);

        //-------- Coupon CRUD Routes --------
        Route::get('coupons',[CouponController::class,'getCoupons']); 
        Route::get('coupon/{id}', [CouponController::class, 'show']);
        Route::post('coupon', [CouponController::class, 'create']);
        Route::put('coupon/{id}', [CouponController::class, 'edit']);
        Route::delete('coupon/{id}', [CouponController::class,'destroy']);

    });

                        // Routes accessed by all roles
    Route::group(['middleware' => ['role:superAdmin|admin|user|staff']], function () {
        // get filtered or all products  
         Route::get('products',[ProductController::class,'getProducts']); 
    });

    
});
?>