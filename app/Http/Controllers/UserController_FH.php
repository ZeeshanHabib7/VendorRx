<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\CrudInterface_FH;
use App\Http\Requests\UserCrudRequest_FH;
use App\Http\Resources\UserResource;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController_FH extends Controller implements CrudInterface_FH {

        //get users with the role assigned 
        public function index()
        {
            try {
                $users = User::with('roles')->get();
                return successResponse("Users fetched successfully!", UserResource::collection($users));
            } 
            catch (\Exception $e) {
                return handleException($e);
            } 
         
        }

        // create user
        public function create(UserCrudRequest_FH $request)
        {
            $validatedData = $request->validated();
            return $this->store($validatedData);
        }
    
        public function store(array $payload)
        {
          try {
            $user = User::create($payload);
            $roles = Role::whereIn('id', $payload['role_ids'])->get();
            $user->syncRoles($roles);
    
            return successResponse('User created successfully', UserResource::make($user));
          }
          catch (\Exception $e) {
            return handleException($e);
          }
        }
    
        // get a specific user by id
        public function show($id)
        {
            try {
                $user = User::with('roles')->findOrFail($id);
                return successResponse("User fetched successfully!", UserResource::make($user));
            }
            catch (\Exception $e) {
                return handleException($e);
            }
        }

        // update user
        public function edit(UserCrudRequest_FH $request, $id)
        {
            $validatedData = $request->validated();
            return $this->update($validatedData,$id);
        }
    
        public function update(array $payload, $id)
        {
            try {
                $user = User::findOrFail($id);
                $user->update($payload);
                if ($payload.contains('role_ids')) {
                    $roles = Role::whereIn('id', $payload['role_ids'])->get();
                    $user->syncRoles($roles);
                }
                return successResponse( 'User updated successfully', UserResource::make($user));
            }
            catch (\Exception $e) {
                return handleException($e);
            }
        }
    
        // delete user
        public function destroy($id)
        {
            try {
                $user = User::findOrFail($id);
                $user->delete();
                return successResponse('User deleted successfully');
            }
            catch (\Exception $e) {
                return handleException($e);
            }
        }
    }