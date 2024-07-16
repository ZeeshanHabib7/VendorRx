<?php

namespace App\Http\Controllers;


use App\Http\Requests\PermissionRequest_SA;
use App\Http\Resources\PermissionResource_SA;
use Spatie\Permission\Models\Permission;

class PermissionController_SA extends Controller
{
    public function store(PermissionRequest_SA $request)
    {

        $permission = Permission::create([
            'name' => $request->name,
        ]);

        return successResponse("Permission created successfully", PermissionResource_SA::make($permission));

    }

    public function show($permissionId)
    {
        try {
            $permission = $this->findPermissionById($permissionId);
            return successResponse("Permission fetched successfully", PermissionResource_SA::make($permission));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }
    }

    public function update(PermissionRequest_SA $request, $permissionId)
    {

        try {
            $permission = $this->findPermissionById($permissionId);
            $permission->update([
                'name' => $request->name,
            ]);

            return successResponse("Permission updated successfully", PermissionResource_SA::make($permission));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    public function destroy($permissionId)
    {
        try {
            $permission = $this->findPermissionById($permissionId);
            $permission->delete();

            return successResponse("Permission deleted successfully", PermissionResource_SA::make($permission));

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());
        }

    }

    protected function findPermissionById($permissionId)
    {
        return Permission::findById($permissionId);
    }
}
