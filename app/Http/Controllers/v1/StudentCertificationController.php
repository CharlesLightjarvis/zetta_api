<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\V1\StudentCertificationService;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class StudentCertificationController extends Controller
{
    use ApiResponse;

    protected $studentCertificationService;

    public function __construct(StudentCertificationService $studentCertificationService)
    {
        $this->studentCertificationService = $studentCertificationService;
    }

    public function index()
    {
        $certifications = $this->studentCertificationService->getStudentCertifications($this->authStudentId());
        return $this->successResponse('Student certifications retrieved successfully', 'certifications', $certifications);
    }

    public function show($certificationId)
    {
        $certification = $this->studentCertificationService->getCertificationDetails($this->authStudentId(), $certificationId);
        return $this->successResponse('Certification details retrieved successfully', 'certification', $certification);
    }

    public function getQuizQuestions($certificationId)
    {
        $questions = $this->studentCertificationService->getCertificationQuizQuestions($this->authStudentId(), $certificationId);
        return $this->successResponse('Quiz questions retrieved successfully', 'questions', $questions);
    }



    public function submitQuiz(Request $request, $certificationId)
    {
        $result = $this->studentCertificationService->submitQuiz(
            $this->authStudentId(),
            $certificationId,
            $request->input('answers')  // Correction ici
        );

        return $this->successResponse(
            $result['passed'] ? 'Quiz completed successfully!' : 'Quiz completed',
            'quiz_result',
            $result
        );
    }

    public function getQuizResult($certificationId, $progressTrackingId)
    {
        $result = $this->studentCertificationService->getQuizResult(
            $this->authStudentId(),
            $certificationId,
            $progressTrackingId
        );

        return $this->successResponse(
            'Quiz result retrieved successfully',
            'quiz_result',
            $result
        );
    }

    public function authStudentId()
    {
        return Auth::id();
    }
}
