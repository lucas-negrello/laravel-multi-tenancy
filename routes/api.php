<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Landlord\PermissionController;
use App\Http\Controllers\Landlord\RoleController;
use App\Http\Controllers\Landlord\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);


    Route::group([
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => ['landlord.context'],
    ], function() {
        Route::apiResources([
            'users' => UserController::class,
            'roles' => RoleController::class,
            'permissions' => PermissionController::class,
        ]);
    });

    Route::group([
        'prefix' => 'tenant',
        'as' => 'tenant.',
        'middleware' => ['tenant.context'],
    ], function() {
        Route::apiResources([
            'users' => UserController::class,
            'roles' => RoleController::class,
            'permissions' => PermissionController::class,
        ]);
    });

});
