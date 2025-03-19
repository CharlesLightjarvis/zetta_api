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
    public function show(Formation $formation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormationRequest $request, Formation $formation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation)
    {
        //
    }
}
