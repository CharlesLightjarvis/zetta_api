<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\FormationResource;
use App\Http\Resources\v1\UserResource;
use App\Models\Category;
use App\Models\Formation;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class FormationService
{
    public function getAllFormations()
    {
        return FormationResource::collection(Formation::with('certifications', 'modules.lessons', 'sessions.teacher')->get());
    }

    public function getFormationById($id)
    {
        return new FormationResource(Formation::with('certifications', 'modules.lessons', 'sessions.teacher')->findOrFail($id));
    }

    public function getFormationBySlug($slug)
    {
        return new FormationResource(Formation::with('certifications', 'modules.lessons', 'sessions.teacher')->where('slug', $slug)->firstOrFail());
    }

    public function createFormation($data)
    {
        try {
            DB::beginTransaction();

            // Gestion de l'image
            if (isset($data['image']) && $data['image']->isValid()) {
                $imagePath = $data['image']->store('formations', 'public');
                $data['image'] = $imagePath;
            }

            $slug = Str::slug($data['name']);
            $formation = Formation::where('slug', $slug)->exists();
            if ($formation) {
                return false;
            }
            $data['slug'] = $slug;

            $category = Category::find($data['category_id']);
            if (!$category) {
                return false;
            }

            // Traiter les prerequisites et objectives
            $data['prerequisites'] = isset($data['prerequisites']) ? array_filter($data['prerequisites']) : null;
            $data['objectives'] = isset($data['objectives']) ? array_filter($data['objectives']) : null;

            $formation = Formation::create($data);

            // Attacher les modules si fournis
            if (isset($data['module_ids']) && is_array($data['module_ids'])) {
                $modules = Module::whereIn('id', $data['module_ids'])->get();
                if ($modules->count() > 0) {
                    $formation->modules()->attach($modules);
                }
            }

            // Créer les sessions si fournies
            if (isset($data['sessions']) && is_array($data['sessions'])) {
                foreach ($data['sessions'] as $sessionData) {
                    $sessionData['formation_id'] = $formation->id;
                    $formation->sessions()->create($sessionData);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
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


            // Gestion de l'image
            if (isset($data['image']) && $data['image']->isValid()) {
                // Supprimer l'ancienne image si elle existe
                if ($formation->image) {
                    Storage::disk('public')->delete($formation->image);
                }
                $imagePath = $data['image']->store('formations', 'public');
                $data['image'] = $imagePath;
            }

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

            if (isset($data['category_id'])) {
                $category = Category::find($data['category_id']);
                if (!$category) {
                    return false;
                }
            }

            if (isset($data['prerequisites'])) {
                $data['prerequisites'] = array_filter($data['prerequisites']);
            }
            if (isset($data['objectives'])) {
                $data['objectives'] = array_filter($data['objectives']);
            }

            $formation->update($data);

            // Mise à jour des modules si fournis
            if (isset($data['module_ids'])) {
                $modules = Module::whereIn('id', $data['module_ids'])->get();
                $formation->modules()->sync($modules);
            }

            // Mise à jour des sessions si fournies
            if (isset($data['sessions'])) {
                $existingSessionIds = $formation->sessions()->pluck('id')->toArray();
                $updatedSessionIds = [];

                foreach ($data['sessions'] as $sessionData) {
                    if (isset($sessionData['id'])) {
                        // Mise à jour d'une session existante
                        $session = $formation->sessions()->find($sessionData['id']);
                        if ($session) {
                            $session->update($sessionData);
                            $updatedSessionIds[] = $session->id;
                        }
                    } else {
                        // Création d'une nouvelle session
                        $sessionData['formation_id'] = $formation->id;
                        $session = $formation->sessions()->create($sessionData);
                        $updatedSessionIds[] = $session->id;
                    }
                }

                // Suppression des sessions qui ne sont plus dans la liste
                $sessionsToDelete = array_diff($existingSessionIds, $updatedSessionIds);
                if (!empty($sessionsToDelete)) {
                    $formation->sessions()->whereIn('id', $sessionsToDelete)->delete();
                }
            }

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

    public function enrollExistingStudent(string $formationId, string $studentId): bool
    {
        try {
            DB::beginTransaction();

            // 1. Vérifier si l'étudiant existe et a le rôle étudiant
            $student = User::findOrFail($studentId);
            if (!$student->hasRole('student')) {
                throw new \Exception('User must be a student to enroll');
            }

            // 2. Vérifier si la formation existe
            $formation = Formation::findOrFail($formationId);

            // 3. Vérifier si l'étudiant n'est pas déjà inscrit à la formation
            if ($formation->students()->where('users.id', $studentId)->exists()) {
                throw new \Exception('Student already enrolled in this formation');
            }

            // 4. Inscrire à la formation
            $formation->students()->attach($studentId);

            // 5. Inscrire à la première session disponible
            $session = $formation->sessions()
                ->where('enrolled_students', '<', DB::raw('capacity'))
                ->orderBy('start_date')
                ->first();

            if (!$session) {
                throw new \Exception('No available session found');
            }

            // 6. Vérifier si l'étudiant n'est pas déjà inscrit à la session
            if (!$session->students()->where('users.id', $studentId)->exists()) {
                $session->students()->attach($studentId);
                $session->increment('enrolled_students');
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to enroll existing student', [
                'student_id' => $studentId,
                'formation_id' => $formationId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function unenrollStudent(string $formationId, string $studentId): bool
    {
        try {
            DB::beginTransaction();

            // 1. Vérifier si la formation existe
            $formation = Formation::findOrFail($formationId);

            // 2. Vérifier si l'étudiant est inscrit à la formation
            if (!$formation->students()->where('users.id', $studentId)->exists()) {
                throw new \Exception('Student not enrolled in this formation');
            }

            // 3. Trouver la session où l'étudiant est inscrit
            $session = $formation->sessions()
                ->whereHas('students', function ($query) use ($studentId) {
                    $query->where('users.id', $studentId);
                })
                ->first();

            // 4. Si l'étudiant est inscrit à une session, le désinscrire
            if ($session) {
                $session->students()->detach($studentId);
                $session->decrement('enrolled_students');
            }

            // 5. Désinscrire de la formation
            $formation->students()->detach($studentId);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to unenroll student', [
                'student_id' => $studentId,
                'formation_id' => $formationId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getEnrolledStudents($formationId)
    {
        $formation = Formation::findOrFail($formationId);
        $students = $formation->students()
            ->select('users.id', 'users.fullName', 'users.email', 'users.imageUrl')
            ->withPivot('created_at')
            ->orderBy('users.fullName')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'fullName' => $student->fullName,
                    'email' => $student->email,
                    'imageUrl' => $student->imageUrl,
                    'created_at' => $student->pivot->created_at->format('Y-m-d H:i')
                ];
            });

        return $students;
    }
}
