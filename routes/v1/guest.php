<?php

use App\Http\Controllers\v1\FormationSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\FormationController;
use App\Http\Controllers\v1\CertificationController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\ModuleController;
use App\Http\Controllers\v1\LessonController;
use App\Http\Controllers\v1\FormationInterestController;

Route::prefix('guest')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('lessons', LessonController::class);
    Route::apiResource('sessions', FormationSessionController::class);
    Route::apiResource('modules', ModuleController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::get('formations/slug/{slug}', [FormationController::class, 'getFormationBySlug']);
    Route::apiResource('formations', FormationController::class);
    Route::apiResource('certifications', CertificationController::class);
    Route::apiResource('interests', FormationInterestController::class);
});
