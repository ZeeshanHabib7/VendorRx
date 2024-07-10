<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserSignUpRequest;
use App\Models\User;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function signUp(UserSignUpRequest $request) {
        try {
            // created model instance
            $user = new User();
            // calling create user function from model
            $user = $user->createUser($request);
            // generate token function call
            $token = $this->generateToken($user);
            // data to be passed in resource file
            $data = [
                'token' => $token,
                'user' => $user
            ];
            // Success response upon user signup
            return successResponse(  "User Registered Sucessfully!",UserResource::make($data));
        }  catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function generateToken($user) {
        return auth('api')->login($user); 
    }

    public function login(UserLoginRequest $request)
    {
        try{
            // get email and password entered by user
            $credentials = $request->only('email', 'password');
            // authenticate user with given credentials
            $token = auth('api')->attempt($credentials);
            // check for unauthorized user with no token
            if (!$token) {
                return errorResponse("Unauthorized access - token is missing or invalid.",401);
            } 
            // get authenticated user
            $user = auth('api')->user();
            // data to be passed in resource file
            $data = [
                'token' => $token,
                'user' => $user
            ];
            // Success response upon user login
            return successResponse(  "User Logged-in Sucessfully!",UserResource::make($data));
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function logout()
    {
        try{
            auth('api')->logout();
            // Success response upon user logout
            return successResponse(  "User Logged-out Sucessfully!");
        }  catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }
}
