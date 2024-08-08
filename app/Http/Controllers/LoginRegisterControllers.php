<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserRegisterRequest_SA;
use App\Http\Requests\UserLoginRequest_SA;
use App\Mail\ForgetPasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource_SA;
use App\Http\Resources\UserResource;


use Auth;
use Mail;
use URL;

class LoginRegisterControllers extends Controller
{

    public function register(UserRegisterRequest_SA $request)
    {
        $user = new User;
        $user = $user->createUser($request);  // creating a new user
        $token = $this->getToken($request);   // generrating a token for user data

        $data = [
            'token' => $token,
            'user' => $user,
        ];

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

    public function forgetPassword()
    {

        return view("forget-password");
    }

    public function sendEmail(ForgetPasswordRequest $request)
    {
        try {

            $user = User::where('email', $request->email)->firstOrFail();

            if (!is_null($user)) {
                $domain = URL::to('/');
                $url = $domain . '/users/reset-password/' . $user->id;
                $data = [
                    "url" => $url,
                    "email" => $user->email,
                    "title" => "Reset Password Link",
                    "body" => "Please click on the below button to reset your password",
                ];

                Mail::to($request->email)->send(new ForgetPasswordMail($data));

                return successResponse("Mail sent successfully. Please check your Inbox");
            }

        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function resetPasswordPageLoad($id)
    {
        try {

            return view("reset-password")->with('id', $id);

        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }

    }

    public function resetPassword(ResetPasswordRequest $request, $userId)
    {
        try {

            $user = User::where('id', $userId)->firstOrFail();

            $user->password = $request->password;
            $user->save();

            return successResponse("Password Changed successfully");

        } catch (\Exception $e) {

            return errorResponse($e->getMessage());

        }
    }
}
