<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\AttendanceResource;
use App\Models\Attendance;
use App\Models\FormationSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    // Récupérer les présences d'une session pour une date
    public function getSessionAttendance(string $sessionId, string $date)
    {
        return AttendanceResource::collection(
            Attendance::with(['student', 'session'])
                ->where('session_id', $sessionId)
                ->where('date', $date)
                ->get()
        );
    }

    // Enregistrer les présences d'une session pour une date
    public function recordAttendance(array $data)
    {
        try {
            Log::info('Starting recordAttendance', [
                'session_id' => $data['session_id'],
                'date' => $data['date'],
                'attendances_count' => count($data['attendances'])
            ]);

            DB::beginTransaction();

            $session = FormationSession::findOrFail($data['session_id']);
            Log::info('Session found', ['session' => $session->id]);

            $date = $data['date'];

            // Supprimer les anciennes présences pour cette session/date
            $deletedCount = Attendance::where('session_id', $session->id)
                ->where('date', $date)
                ->delete();
            Log::info('Deleted old attendances', ['count' => $deletedCount]);

            // Enregistrer les nouvelles présences
            $created = [];
            foreach ($data['attendances'] as $attendance) {
                try {
                    $newAttendance = Attendance::create([
                        'session_id' => $session->id,
                        'student_id' => $attendance['student_id'],
                        'date' => $date,
                        'status' => $attendance['status'],
                        'notes' => $attendance['notes'] ?? null,
                    ]);

                    Log::info('Created attendance', [
                        'student_id' => $attendance['student_id'],
                        'status' => $attendance['status']
                    ]);

                    $created[] = $newAttendance->id;
                } catch (\Exception $e) {
                    Log::error('Failed to create attendance', [
                        'student_id' => $attendance['student_id'],
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            Log::info('All attendances created successfully', [
                'total_created' => count($created),
                'attendance_ids' => $created
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('recordAttendance failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            return false;
        }
    }

    // Consulter l'historique des présences d'un étudiant
    public function getStudentAttendance(string $studentId, string $sessionId)
    {
        return AttendanceResource::collection(
            Attendance::with(['student', 'session'])
                ->where('student_id', $studentId)
                ->where('session_id', $sessionId)
                ->orderBy('date', 'desc')
                ->get()
        );
    }
}
