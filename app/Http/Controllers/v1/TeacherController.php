<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\V1\TeacherService;
use App\Trait\ApiResponse;

class TeacherController extends Controller
{
    use ApiResponse;

    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    public function sessions()
    {
        $sessions = $this->teacherService->getTeacherSessions();
        return $this->successResponse('Sessions retrieved successfully', 'sessions', $sessions);
    }
}
