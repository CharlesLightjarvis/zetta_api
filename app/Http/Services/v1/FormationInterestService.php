<?php

namespace App\Http\Services\V1;

use App\Events\NewFormationInterest;
use App\Http\Resources\v1\FormationInterestResource;
use App\Models\FormationInterest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormationInterestService
{

    public function getAllInterests()
    {
        return FormationInterestResource::collection(FormationInterest::with('formation')->get());
    }

    public function createInterest($data)
    {
        try {
            DB::beginTransaction();

            $interest = FormationInterest::create($data);
            Log::info('New interest created', ['interest' => $interest]);

            // Load the formation relationship
            $interest->load('formation');

            // Broadcast the event
            broadcast(new NewFormationInterest($interest))->toOthers();

            // DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
