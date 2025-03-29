<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Trait\ApiResponse;

class RoleController extends Controller
{
    use ApiResponse;
    public function __invoke()
    {
        $roles = Role::all();
        return $this->successResponse('Roles retrieved successfully', 'roles', $roles);
    }
}
