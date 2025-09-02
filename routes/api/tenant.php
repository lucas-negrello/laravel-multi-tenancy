<?php

use App\Http\Controllers\Api\Landlord\RoleController;
use App\Http\Controllers\Api\Landlord\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'tenant',
    'as' => 'tenant.',
    'middleware' => ['tenant.context'],
], function() {
    Route::apiResources([
        'users' => UserController::class,
        'roles' => RoleController::class,
    ]);
});
