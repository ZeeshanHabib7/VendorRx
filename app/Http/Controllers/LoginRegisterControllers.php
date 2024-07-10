<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest_SA;
use App\Http\Requests\UserLoginRequest_SA;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource_SA;
use App\Http\Resources\UserResource;


use Auth;

class LoginRegisterControllers extends Controller
{

    public function register(UserRegisterRequest_SA $request)
    {
        $user = new User;
        $user = $user->createUser($request);
        $token = $this->getToken($request);

        $data = [
            'token' => $token,  // generrating a token for user data
            'user' => $user,        // creating a new user
        ];
        // dd($data);
        return successResponse("User Registered Sucessfully!", UserResource_SA::make($data));

    }

    public function login(UserLoginRequest_SA $request)
    {

        if (!$token = $this->getToken($request)) {
            return errorResponse("Unauthenticated User", 401, );
        }

        $data = [
            'token' => $token,
            'user' => auth()->user(),
        ];


        return successResponse("User Logged in Sucessfully!", UserResource_SA::make($data));

    }


    protected function getToken(Request $request)
    {

        return Auth::guard('api')->attempt($request->only('email', 'password'));
    }

}
