<?php

use App\Http\Controllers\v1\CertificationController;
use App\Http\Controllers\v1\FormationController;
use App\Http\Controllers\v1\FormationInterestController;
use App\Http\Controllers\v1\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\CategoryController;



Route::prefix('admin')->group(function () {
    Route::get('roles', [RoleController::class, '__invoke']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('certifications', CertificationController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('formations', FormationController::class);
    Route::apiResource('interests', FormationInterestController::class);
});
