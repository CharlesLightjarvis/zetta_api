<?php

namespace App\Http\Services\V1;

use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function getAllUsers()
    {
        return  UserResource::collection(User::all());;
    }

    public function getUserById($id)
    {
        return new UserResource(User::findOrFail($id));
    }

    public function createUser($data)
    {
        try {
            DB::beginTransaction();

            $user = User::where('email', $data['email'])->exists();
            if ($user) {
                return false;
            }

            $password = Str::random(8);
            $data['password'] = Hash::make($password);

            $user = User::create($data);

            // !INFO handle the imageUrl when giving with spatie medialibrary 

            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateUser($id, $data)
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);

            if (!$user) return false;

            $user->update($data);
            if (isset($data['role'])) {
                $user->syncRoles($data['role']);
            }

            // !INFO handle the imageUrl when giving with spatie medialibrary 

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return false;
            }

            $user->syncRoles([]);
            // $user->removeMedia();
            $user->delete(); // Suppression définitive

            return true;
        } catch (\Exception $e) {

            return false;
        }
    }
}
