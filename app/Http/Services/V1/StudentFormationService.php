<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\FormationResource;
use App\Models\User;

class StudentFormationService
{
    public function getStudentFormations($studentId)
    {
        $user = User::findOrFail($studentId);
        $formations = $user->formations()
            ->with([
                'category',
                'certifications',
                'modules.lessons',
                'sessions' => function ($query) {
                    $query->orderBy('start_date', 'desc');
                },
                'sessions.teacher'
            ])
            ->get();

        return FormationResource::collection($formations);
    }

    public function getFormationDetails($studentId, $formationId)
    {
        $user = User::findOrFail($studentId);
        $formation = $user->formations()
            ->with([
                'category',
                'certifications',
                'modules.lessons',
                'sessions' => function ($query) {
                    $query->orderBy('start_date', 'desc');
                },
                'sessions.teacher'
            ])
            ->where('formations.id', $formationId)
            ->firstOrFail();

        return new FormationResource($formation);
    }
}
