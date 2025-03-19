<?php

use App\Http\Controllers\v1\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\FormationController;
use App\Http\Controllers\v1\CertificationController;


Route::prefix('admin')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('formations', FormationController::class);
    Route::apiResource('certifications', CertificationController::class);
});
