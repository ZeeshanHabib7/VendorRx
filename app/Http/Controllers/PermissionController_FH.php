<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\CrudInterface_FH;
use App\Http\Requests\PermissionRequest_FH;
use App\Http\Resources\RolePermissionResource_FH;
use Spatie\Permission\Models\Permission;

class PermissionController_FH extends Controller implements CrudInterface_FH
{
    public function index()
    {
        try {
            // get all permissions
            $permissions = Permission::all();
            // success response upon permission fetched
            return successResponse("Permission Fetched Successfully!", RolePermissionResource_FH::collection($permissions));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function create(PermissionRequest_FH $request) 
    {
        $validatedData = $request->validated();
        return $this->store($validatedData);
    }

    public function store(array $payload)
    { 
        try {
            // create permission
            $permission = Permission::create($payload);
            // success reponse upon creation
            return successResponse("Permission Created Successfully!", RolePermissionResource_FH::make($permission));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function edit(PermissionRequest_FH $request, $id)
    {
        $validatedData = $request->validated();
        return $this->update($validatedData,$id);
    }


    public function update(array $payload, $id)
    {
        try{
            $permission = Permission::findOrFail($id);
            // update permission
            $permission->update($payload);  
            // get updated permission
            $updatedPermission = Permission::find($permission->id);  
            // success response upon updation
            return successResponse("Permission Updated Successfully!",RolePermissionResource_FH::make($updatedPermission));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }    
        
    }

    public function destroy($permissionId)
    {
        try {
            // delete permission
            $permission = Permission::find($permissionId);
            $permission->delete();
            // success response upon deletion
            return successResponse("Permission Deleted Successfully!");
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }
}
