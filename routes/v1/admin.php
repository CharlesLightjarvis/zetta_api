<?php

use App\Http\Controllers\v1\AdminController;
use App\Http\Controllers\v1\QuizController;
use App\Http\Controllers\v1\CertificationController;
use App\Http\Controllers\v1\FormationController;
use App\Http\Controllers\v1\FormationInterestController;
use App\Http\Controllers\v1\FormationSessionController;
use App\Http\Controllers\v1\LessonController;
use App\Http\Controllers\v1\ModuleController;
use App\Http\Controllers\v1\PaymentController;
use App\Http\Controllers\v1\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\CourseSchedulesController;

Route::prefix('admin')->group(function () {
    Route::get('roles', [RoleController::class, '__invoke']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('lessons', LessonController::class);
    Route::apiResource('modules', ModuleController::class);
    Route::apiResource('sessions', FormationSessionController::class);
    Route::apiResource('certifications', CertificationController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('formations', FormationController::class);
    Route::apiResource('interests', FormationInterestController::class);
    Route::post('interests/{id}/approve', [FormationInterestController::class, 'approve']);
    Route::get('sessions/{sessionId}/students', [FormationSessionController::class, 'getSessionStudents']);
    Route::get('students/{studentId}/sessions-formations', [UserController::class, 'getStudentSessionsAndFormations']);

    Route::post('sessions/{sessionId}/students/{studentId}', [FormationSessionController::class, 'enrollStudent']);
    Route::delete('sessions/{sessionId}/students/{studentId}', [FormationSessionController::class, 'unenrollStudent']);
    // Dans le groupe admin existant
    Route::apiResource('payments', PaymentController::class);
    Route::get('students', [UserController::class, 'getStudents']);

    Route::post('formations/{formation}/enroll', [FormationController::class, 'enrollStudent']);
    Route::delete('formations/{formation}/unenroll/{studentId}', [FormationController::class, 'unenrollStudent']);

    Route::get('formations/{formation}/students', [FormationController::class, 'getEnrolledStudents']);

    Route::get('statistics', [AdminController::class, 'getStatistics']);

    // Routes pour les quiz
    Route::prefix('quiz')->group(function () {
        Route::post('configurations', [QuizController::class, 'storeConfiguration']);
        Route::get('configurations', [QuizController::class, 'getAllConfigurations']);
        Route::get('configurations/{id}', [QuizController::class, 'getConfiguration']);
        Route::put('configurations/{id}', [QuizController::class, 'updateConfiguration']);  // Nouvelle route
        Route::delete('configurations/{id}', [QuizController::class, 'deleteConfiguration']);  // Nouvelle route

        Route::post('questions', [QuizController::class, 'storeQuestion']);
        Route::get('questions', [QuizController::class, 'getAllQuestions']);
        Route::get('questions/{id}', [QuizController::class, 'getQuestion']);
        Route::put('questions/{id}', [QuizController::class, 'updateQuestion']);  // Nouvelle route
        Route::delete('questions/{id}', [QuizController::class, 'deleteQuestion']);  // Nouvelle route

        Route::get('generate-quiz/{configId}/{type?}', [QuizController::class, 'generateQuiz']);

    });

    Route::get('certifications/{certificationId}/modules', [QuizController::class, 'getCertificationModules']);
});

