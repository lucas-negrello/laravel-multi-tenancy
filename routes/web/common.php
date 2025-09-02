<?php

use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Common\HomeController;
use App\Http\Controllers\Web\Common\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group([
    'prefix' => 'users',
    'as' => 'users.'
], function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/data', [UserController::class, 'data'])->name('data');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});


Route::get('/.well-known/appspecific/com.chrome.devtools.json', function () {
    return response()->noContent();
})->name('wellknown.chrome-devtools');
