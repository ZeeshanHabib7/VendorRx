<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\CrudInterface_FH;
use App\Http\Requests\RoleRequest_FH;
use App\Http\Resources\RolePermissionResource_FH;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController_FH extends Controller implements CrudInterface_FH
{
    public function index()
    {
        try {
            // get all roles
            $roles = Role::all();
            // success response upon roles fetched
            return successResponse("Roles Fetched Successfully!", RolePermissionResource_FH::collection($roles));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function create(RoleRequest_FH $request){
        $validatedData = $request->validated();
        return $this->store($validatedData);
    }

    public function store(array $payload)
    {
        try {
            // create role
            $role = Role::create($payload);
            // success reponse upon creation
            return successResponse("Role Created Successfully!", RolePermissionResource_FH::make($role));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function edit(RoleRequest_FH $request, $id)
    {
        $validatedData = $request->validated();
        return $this->update($validatedData,$id);
    }

    public function update(array $payload, $roleId)
    {
        try{
            $role = Role::findOrFail($roleId);
            // update role
            $role->update($payload);  
            // get updated role
            $updatedRole = Role::find($role->id);  
            // success response upon updation
            return successResponse("Role Updated Successfully!",RolePermissionResource_FH::make($updatedRole));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }  
    }

    public function destroy($roleId)
    {
        try {
            // delete role
            $role = Role::find($roleId);
            $role->delete();
            // success response upon deletion
            return successResponse("Role Deleted Successfully!");
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

       // Add or sync permissions to a role
       public function updatePermissions(Request $request, $roleId)
       {
            try{
                $role = Role::findOrFail($roleId);
                
                $permissions = $request->input('permission_ids');
                if (is_array($permissions)) {
                    $permissions = Permission::whereIn('id', $permissions)->get();
                    $role->syncPermissions($permissions);
                    $message = 'Permissions synchronized with role successfully';
                } else {
                    $permission = Permission::findOrFail($permissions);
                    $role->givePermissionTo($permission);
                    $message = 'Permission given to role successfully';
                }
        
                return successResponse( $message);
            }
            catch (\Exception $e) {
                // Handle any exceptions that may occur during the process
                return handleException($e);
            }

       }
}
