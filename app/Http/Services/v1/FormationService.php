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
        return FormationResource::collection(Formation::with('certifications', 'modules.lessons')->get());
    }

    public function getFormationById($id)
    {
        return new FormationResource(Formation::with('certifications', 'modules.lessons')->findOrFail($id));
    }

    public function getFormationBySlug($slug)
    {
        return new FormationResource(Formation::with('certifications', 'modules.lessons')->where('slug', $slug)->firstOrFail());
    }

    public function createFormation($data)
    {
        try {
            DB::beginTransaction();
            $slug = Str::slug($data['name']);
            $formation = Formation::where('slug', $slug)->exists();
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

            // Traiter les prerequisites et objectives
            $data['prerequisites'] = isset($data['prerequisites']) ? array_filter($data['prerequisites']) : null;
            $data['objectives'] = isset($data['objectives']) ? array_filter($data['objectives']) : null;

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
        try {
            DB::beginTransaction();

            $formation = Formation::find($id);
            if (!$formation) {
                return false;
            }

            // Si le nom est modifié, vérifier que le nouveau slug n'existe pas déjà
            if (isset($data['name'])) {
                $newSlug = Str::slug($data['name']);
                if ($newSlug !== $formation->slug) {
                    $slugExists = Formation::where('slug', $newSlug)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($slugExists) {
                        return false;
                    }
                    $data['slug'] = $newSlug;
                }
            }

            // Vérifier l'enseignant si l'ID est modifié
            if (isset($data['teacher_id'])) {
                $user = User::find($data['teacher_id']);
                if (!$user || !$user->hasRole('teacher')) {
                    return false;
                }
            }

            // Vérifier la catégorie si l'ID est modifié
            if (isset($data['category_id'])) {
                $category = Category::find($data['category_id']);
                if (!$category) {
                    return false;
                }
            }

            // Traiter les prerequisites et objectives
            if (isset($data['prerequisites'])) {
                $data['prerequisites'] = array_filter($data['prerequisites']);
            }
            if (isset($data['objectives'])) {
                $data['objectives'] = array_filter($data['objectives']);
            }

            $formation->update($data);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function deleteFormation($id)
    {
        $formation = Formation::find($id);
        return $formation ? $formation->delete() : false;
    }
}
