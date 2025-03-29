<?php

use App\Http\Controllers\v1\FormationInterestController;
use App\Http\Controllers\v1\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController;



Route::prefix('admin')->group(function () {
    Route::get('roles', [RoleController::class, '__invoke']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('interests', FormationInterestController::class);
});
