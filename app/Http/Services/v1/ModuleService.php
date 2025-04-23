<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\ModuleResource;
use App\Models\Formation;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleService
{
    public function getAllModules()
    {
        return ModuleResource::collection(
            Module::with('formations', 'lessons')->get()
        );
    }

    public function getModuleById($id)
    {
        return new ModuleResource(
            Module::with('formations', 'lessons')->findOrFail($id)
        );
    }

    public function createModule($data)
    {
        try {
            DB::beginTransaction();

            // Vérification du slug unique
            $slug = Str::slug($data['name']);
            $moduleExists = Module::where('slug', $slug)->exists();
            if ($moduleExists) {
                return false;
            }

            // Création du module
            $module = Module::create([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
            ]);

            // Attacher les formations si fournies
            if (isset($data['formation_ids']) && is_array($data['formation_ids'])) {
                $formations = Formation::whereIn('id', $data['formation_ids'])->get();
                if ($formations->count() > 0) {
                    $module->formations()->attach($formations);
                }
            }

            // Création des leçons si fournies
            if (isset($data['lessons']) && is_array($data['lessons'])) {
                foreach ($data['lessons'] as $lessonData) {
                    $module->lessons()->create([
                        'name' => $lessonData['name'],
                        'slug' => Str::slug($lessonData['name']),
                        'description' => $lessonData['description'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateModule($id, $data)
    {
        try {
            DB::beginTransaction();

            $module = Module::find($id);
            if (!$module) {
                return false;
            }

            // Si le nom est modifié, vérifier que le nouveau slug n'existe pas déjà
            if (isset($data['name'])) {
                $newSlug = Str::slug($data['name']);
                if ($newSlug !== $module->slug) {
                    $slugExists = Module::where('slug', $newSlug)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($slugExists) {
                        return false;
                    }
                    $data['slug'] = $newSlug;
                }
            }

            $module->update([
                'name' => $data['name'] ?? $module->name,
                'slug' => $data['slug'] ?? $module->slug,
                'description' => $data['description'] ?? $module->description,
            ]);

            // Mise à jour des formations si fournies
            if (isset($data['formation_ids'])) {
                $formations = Formation::whereIn('id', $data['formation_ids'])->get();
                $module->formations()->sync($formations);
            }

            // Mise à jour des leçons si fournies
            if (isset($data['lessons']) && is_array($data['lessons'])) {
                // Supprimer les leçons existantes
                $module->lessons()->delete();

                // Créer les nouvelles leçons
                foreach ($data['lessons'] as $lessonData) {
                    $module->lessons()->create([
                        'name' => $lessonData['name'],
                        'slug' => Str::slug($lessonData['name']),
                        'description' => $lessonData['description'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function deleteModule($id)
    {
        try {
            DB::beginTransaction();

            $module = Module::find($id);
            if (!$module) {
                return false;
            }

            // Les relations avec les formations seront automatiquement supprimées 
            // grâce à la configuration de la table pivot

            // Supprimer les leçons
            $module->lessons()->delete();

            // Supprimer le module
            $module->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
