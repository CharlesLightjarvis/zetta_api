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
        return ModuleResource::collection(Module::with('formation', 'lessons')->get());
    }
    public function getModuleById($id)
    {
        return new ModuleResource(Module::with('formation', 'lessons')->findOrFail($id));
    }

    public function createModule($data)
    {
        try {
            DB::beginTransaction();

            // Vérification du slug unique
            $slug = Str::slug($data['name']);
            $module = Module::where('slug', $slug)->exists();
            if ($module) {
                return false;
            }

            // Vérification si la formation existe
            $formation = Formation::find($data['formation_id']);
            if (!$formation) {
                return false;
            }

            // Ajout du slug aux données
            $data['slug'] = $slug;

            // Création du module
            $module = Module::create($data);

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

            // Vérifier la formation si l'ID est modifié
            if (isset($data['formation_id'])) {
                $formation = Formation::find($data['formation_id']);
                if (!$formation) {
                    return false;
                }
            }

            $module->update($data);

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
            $module = Module::find($id);
            return $module ? $module->delete() : false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
