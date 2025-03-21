<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\FormationSessionResource;
use App\Models\Formation;
use App\Models\FormationSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FormationSessionService
{
    public function getAllSessions()
    {
        return FormationSessionResource::collection(FormationSession::with('formation', 'teacher')->get());
    }

    public function getSessionById($id)
    {
        return new FormationSessionResource(FormationSession::with('formation', 'teacher')->findOrFail($id));
    }

    public function createSession($data)
    {
        try {
            DB::beginTransaction();

            // Vérifier si la formation existe
            $formation = Formation::find($data['formation_id']);
            if (!$formation) {
                return false;
            }

            // Vérifier si l'enseignant existe (si fourni)
            if (isset($data['teacher_id'])) {
                $teacher = User::find($data['teacher_id']);
                if (!$teacher || !$teacher->hasRole('teacher')) {
                    return false;
                }
            }

            // Créer la session
            $session = FormationSession::create($data);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateSession($id, $data)
    {
        try {
            DB::beginTransaction();

            $session = FormationSession::find($id);
            if (!$session) {
                return false;
            }

            // Vérifier la formation si l'ID est modifié
            if (isset($data['formation_id'])) {
                $formation = Formation::find($data['formation_id']);
                if (!$formation) {
                    return false;
                }
            }

            // Vérifier l'enseignant si l'ID est modifié
            if (isset($data['teacher_id'])) {
                $teacher = User::find($data['teacher_id']);
                if (!$teacher || !$teacher->hasRole('teacher')) {
                    return false;
                }
            }

            // Mettre à jour la session
            $session->update($data);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function deleteSession($id)
    {
        $session = FormationSession::find($id);
        return $session ? $session->delete() : false;
    }
}
