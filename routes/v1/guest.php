<?php

use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\FormationController;
use App\Http\Controllers\v1\FormationSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\CertificationController;
use App\Http\Controllers\v1\ModuleController;
use App\Http\Controllers\v1\LessonController;

Route::prefix('guest')->group(function () {
    Route::get('formations', [FormationController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::apiResource('lessons', LessonController::class);
    Route::apiResource('sessions', FormationSessionController::class);
    Route::apiResource('modules', ModuleController::class);
    Route::get('formations/slug/{slug}', [FormationController::class, 'getFormationBySlug']);
    Route::get('certifications', [CertificationController::class, 'index']);
});
