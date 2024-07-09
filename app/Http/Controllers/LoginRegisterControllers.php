<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest_SA;
use App\Http\Requests\UserLoginRequest_SA;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource_SA;

use Auth;

class LoginRegisterControllers extends Controller
{

    public function register(UserRegisterRequest_SA $request)
    {

        $user = User::createUser($request);


        return userResponse("User Registered Sucessfully!", $this->getToken(auth()->attempt($request->all())), UserResource_SA::make($user));
    }

    public function Login(UserLoginRequest_SA $request)
    {

        if (!$token = auth()->attempt($request->only('email', 'password'))) {
            return userResponse("Unauthenticated User", false, 404);
        }
        return userResponse("User Logged in Sucessfully!", $this->getToken($token), UserResource_SA::make(auth()->user()));

    }

    protected function getToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }
}
