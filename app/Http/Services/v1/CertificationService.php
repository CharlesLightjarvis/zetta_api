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
        $certification = Certification::find($id);
        if (!$certification) return false;

        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $certification->update($data);
    }

    public function deleteCertification($id)
    {
        $certification = Certification::find($id);
        return $certification ? $certification->delete() : false;
    }
}
