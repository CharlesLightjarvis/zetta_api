<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Questions\StoreQuestionRequest;
use App\Http\Requests\v1\Questions\UpdateQuestionRequest;
use App\Http\Requests\v1\QuizConfiguration\StoreQuizConfigurationRequest;
use App\Http\Requests\v1\QuizConfiguration\UpdateQuizConfigurationRequest;
use App\Http\Resources\v1\QuizConfigurationResource;
use App\Http\Resources\v1\QuestionResource;
use App\Http\Services\V1\QuizService;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function storeConfiguration(StoreQuizConfigurationRequest $request)
    {
        $quizConfig = $this->quizService->createQuizConfiguration($request->validated());
        return new QuizConfigurationResource($quizConfig);
    }

    public function storeQuestion(StoreQuestionRequest $request)
    {
        $question = $this->quizService->createQuestion($request->validated());
        return new QuestionResource($question);
    }

    public function getConfiguration($id)
    {
        $quizConfig = $this->quizService->getQuizConfiguration($id);
        return new QuizConfigurationResource($quizConfig);
    }

    public function getQuestion($id)
    {
        $question = $this->quizService->getQuestion($id);
        return new QuestionResource($question);
    }

    public function getAllConfigurations()
    {
        $configurations = $this->quizService->getAllQuizConfigurations();
        return QuizConfigurationResource::collection($configurations);
    }

    public function getAllQuestions()
    {
        $questions = $this->quizService->getAllQuestions();
        return QuestionResource::collection($questions);
    }

    public function updateConfiguration(UpdateQuizConfigurationRequest $request, $id)
    {
        try {
            $quizConfig = $this->quizService->updateQuizConfiguration($id, $request->validated());
            return new QuizConfigurationResource($quizConfig);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteConfiguration($id)
    {
        $isDeleted = $this->quizService->deleteQuizConfiguration($id);
        if ($isDeleted) {
            return response()->json(['message' => 'Configuration supprimée avec succès']);
        }
        return response()->json(['message' => 'Configuration non trouvée'], 404);
    }

    public function updateQuestion(UpdateQuestionRequest $request, $id)
    {
        try {
            $question = $this->quizService->updateQuestion($id, $request->validated());
            return new QuestionResource($question);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteQuestion($id)
    {
        $isDeleted = $this->quizService->deleteQuestion($id);
        if ($isDeleted) {
            return response()->json(['message' => 'Question supprimée avec succès']);
        }
        return response()->json(['message' => 'Question non trouvée'], 404);
    }
}
