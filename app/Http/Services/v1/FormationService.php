<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\FormationResource;
use App\Models\Category;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class FormationService
{
    public function getAllFormations()
    {
        return FormationResource::collection(Formation::with('certifications')->get());
    }

    public function getFormationById($id)
    {
        return new FormationResource(Formation::with('certifications')->findOrFail($id));
    }

    public function createFormation($data)
    {
        try {
            DB::beginTransaction();
            $slug = Str::slug($data['name']);
            $formation  = Formation::where('slug', $slug)->exists();
            if ($formation) {
                return false;
            }
            $data['slug'] = $slug;

            // Vérifier si l'enseignant existe et a le rôle 'teacher'
            $user = User::find($data['teacher_id']);
            if (!$user || !$user->hasRole('teacher')) {
                return false;
            }

            $category = Category::find($data['category_id']);
            // Vérifier si la catégorie existe
            if (!$category) {
                return false;
            }

            $formation = Formation::create($data);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateFormation($id, $data)
    {
        $formation = Formation::find($id);
        if (!$formation) return false;

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $formation->update($data);
    }

    public function deleteFormation($id)
    {
        $formation = Formation::find($id);
        return $formation ? $formation->delete() : false;
    }
}
