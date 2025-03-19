<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCertificationRequest;
use App\Http\Requests\v1\Certification\StoreCertificationRequest;
use App\Http\Services\V1\CertificationService;
use App\Models\Certification;
use App\Trait\ApiResponse;

class CertificationController extends Controller
{
    use ApiResponse;

    protected $certificationService;

    public function __construct(CertificationService $certificationService)
    {
        $this->certificationService = $certificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certifications = $this->certificationService->getAllCertifications();
        return $this->successResponse('Certifications retrieved successfully', 'certifications', $certifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCertificationRequest $request)
    {
        $is_created = $this->certificationService->createCertification($request->validated());
        if ($is_created) {
            return $this->successNoData('Certification created successfully');
        }
        return $this->errorResponse('Certification already exists or failed to create check the data', 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Certification $certification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCertificationRequest $request, Certification $certification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certification $certification)
    {
        //
    }
}
