<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function signUp(UserRequest $request) {
        try {
            // created model instance
            $user = new User();
            // calling create user function from model
            $result = $user->createUser($request);
            // Success response upon user signup
            return successResponse(  "User Registered Sucessfully!",UserResource::make($result));

        }  catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }

    }

 
}
