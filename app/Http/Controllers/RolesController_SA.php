<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolesRequest_SA;
use App\Http\Resources\RolesResource_SA;
use App\Interfaces\CRUD_Operations_Interface_SA;
use Illuminate\Http\Request;
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

        return successResponse("Role created successfully", RolesResource_SA::make($role));

    }

    public function show($roleId)
    {
        try {
            $role = $this->findRolesById($roleId);
            return successResponse("Role found successfully", RolesResource_SA::make($role));

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

            return successResponse("Role updated successfully", RolesResource_SA::make($role));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function destroy($roleId)
    {
        try {
            $role = $this->findRolesById($roleId);
            $role->delete();

            return successResponse("Role deleted successfully", RolesResource_SA::make($role));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    protected function findRolesById($roleId)
    {
        return Role::findById($roleId);
    }
}
