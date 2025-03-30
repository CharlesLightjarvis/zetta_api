<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\CertificationResource;
use App\Models\Certification;
use App\Models\Formation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class CertificationService
{
    public function getAllCertifications()
    {
        return CertificationResource::collection(
            Certification::with('formation')->get()
        );
    }

    public function getCertificationById($id)
    {
        return new CertificationResource(Certification::with('formation')->findOrFail($id));
    }

    public function createCertification($data)
    {
        try {
            DB::beginTransaction();

            // Vérification du slug unique
            $slug = Str::slug($data['name']);

            $certification = Certification::where('slug', $slug)->exists();
            if ($certification) {
                return false;
            }

            // Vérification si la formation existe
            $formation = Formation::find($data['formation_id']);
            if (!$formation) {
                return false;
            }

            // Ajout du slug aux données
            $data['slug'] = $slug;

            // S'assurer que les tableaux sont bien initialisés même s'ils sont vides
            $arrayFields = ['benefits', 'skills', 'best_for', 'prerequisites'];
            foreach ($arrayFields as $field) {
                if (!isset($data[$field])) {
                    $data[$field] = [];
                }
            }

            // Création de la certification
            $certification = Certification::create($data);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateCertification($id, $data)
    {
        try {
            DB::beginTransaction();

            $certification = Certification::find($id);
            if (!$certification) {
                return false;
            }

            // Si le nom est modifié, vérifier que le nouveau slug n'existe pas déjà
            if (isset($data['name'])) {
                $newSlug = Str::slug($data['name']);
                if ($newSlug !== $certification->slug) {
                    $slugExists = Certification::where('slug', $newSlug)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($slugExists) {
                        return false;
                    }
                    $data['slug'] = $newSlug;
                }
            }

            // Vérifier la formation si l'ID est modifié
            if (isset($data['formation_id'])) {
                $formation = Formation::find($data['formation_id']);
                if (!$formation) {
                    return false;
                }
            }

            // Gérer les champs de type tableau
            $arrayFields = ['benefits', 'skills', 'best_for', 'prerequisites'];
            foreach ($arrayFields as $field) {
                if (isset($data[$field])) {
                    // S'assurer que le champ est un tableau
                    if (!is_array($data[$field])) {
                        $data[$field] = [];
                    }
                } else {
                    // Conserver les valeurs existantes si non fournies
                    $data[$field] = $certification->$field ?? [];
                }
            }

            $certification->update($data);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function deleteCertification($id)
    {
        $certification = Certification::find($id);
        return $certification ? $certification->delete() : false;
    }
}
