<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolesRequest_SA;
use App\Http\Resources\RolePermissionResource;
use Spatie\Permission\Models\Role;

class RolesController_SA extends Controller
{
    public function store(RolesRequest_SA $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:roles,name'],
        ]);


        $role = Role::create([
            'name' => $request->name,
        ]);

        return successResponse("Role created successfully", RolePermissionResource::make($role));

    }

    public function show($roleId)
    {
        try {
            $role = $this->findRolesById($roleId);
            return successResponse("Role found successfully", RolePermissionResource::make($role));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }
    }

    public function update(RolesRequest_SA $request, $roleId)
    {

        try {
            $role = $this->findRolesById($roleId);
            $role->update([
                'name' => $request->name,
            ]);

            return successResponse("Role updated successfully", RolePermissionResource::make($role));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function destroy($roleId)
    {
        try {
            $role = $this->findRolesById($roleId);
            $role->delete();

            return successResponse("Role deleted successfully", RolePermissionResource::make($role));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    protected function findRolesById($roleId)
    {
        return Role::findById($roleId);
    }
}
