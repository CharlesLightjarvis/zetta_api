<?php

use App\Http\Controllers\v1\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('teacher')->group(function () {
    Route::get('sessions', [TeacherController::class, 'sessions']);
});
