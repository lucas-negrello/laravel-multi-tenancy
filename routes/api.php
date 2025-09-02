<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Landlord\PermissionController;
use App\Http\Controllers\Api\Landlord\RoleController;
use App\Http\Controllers\Api\Landlord\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/auth.php';
require __DIR__ . '/api/common.php';

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/admin.php';
    require __DIR__ . '/api/tenant.php';
});
