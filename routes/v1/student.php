<?php


use App\Http\Controllers\v1\StudentCertificationController;
use Illuminate\Support\Facades\Route;



Route::prefix('student')->group(function () {
    Route::get('certifications', [StudentCertificationController::class, 'index']);
    Route::get('certifications/{certificationId}', [StudentCertificationController::class, 'show']);
    Route::get('certifications/{certificationId}/quiz', [StudentCertificationController::class, 'getQuizQuestions']);

    Route::post('certifications/{certificationId}/quiz/submit', [StudentCertificationController::class, 'submitQuiz']);
    Route::get('certifications/{certificationId}/quiz-results/{progressTrackingId}', [StudentCertificationController::class, 'getQuizResult']);
});
