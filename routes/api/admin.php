<?php

use App\Http\Controllers\Api\Landlord\PermissionController;
use App\Http\Controllers\Api\Landlord\RoleController;
use App\Http\Controllers\Api\Landlord\UserController;
use Illuminate\Support\Facades\Route;

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
