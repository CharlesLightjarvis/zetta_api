<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Formation\StoreFormationRequest;
use App\Http\Requests\v1\Formation\UpdateFormationRequest;
use App\Http\Services\V1\FormationService;
use App\Models\Formation;
use App\Trait\ApiResponse;

class FormationController extends Controller
{
    use ApiResponse;

    protected $formationService;

    public function __construct(FormationService $formationService)
    {
        $this->formationService = $formationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formations = $this->formationService->getAllFormations();
        return $this->successResponse('Formations retrieved successfully', 'formations', $formations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormationRequest $request)
    {
        $is_created = $this->formationService->createFormation($request->validated());
        if ($is_created) {
            return $this->successNoData('Formation created successfully');
        }
        return $this->errorResponse('Formation already exists or failed to create check the data', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $formation = $this->formationService->getFormationById($id);
        return $this->successResponse('Formation retrieved successfully', 'formation', $formation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormationRequest $request, $id)
    {
        $is_updated = $this->formationService->updateFormation($id, $request->validated());
        if ($is_updated) {
            return $this->successNoData('Formation updated successfully');
        }
        return $this->errorResponse('Formation not found or cannot be updated', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $is_deleted = $this->formationService->deleteFormation($id);
        if ($is_deleted) {
            return $this->successNoData('Formation deleted successfully');
        }
        return $this->errorResponse('Formation not found', 404);
    }

    public function getFormationBySlug($slug)
    {
        $formation = $this->formationService->getFormationBySlug($slug);
        return $this->successResponse('Formation retrieved successfully', 'formation', $formation);
    }
}
