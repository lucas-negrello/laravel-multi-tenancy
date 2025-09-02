<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/web/public.php';

Route::middleware('auth')->group(function () {

    require __DIR__ . '/web/common.php';

    require __DIR__ . '/web/admin.php';

    require __DIR__ . '/web/tenant.php';
});


Route::fallback(function () {
    return redirect()->route('root');
})->name('fallback.root');
