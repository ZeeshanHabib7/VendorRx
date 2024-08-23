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
            return errorResponse("Unauthenticated User", 401,);
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
            // Attempt to find the user by the provided email address.
            // If the user is not found, firstOrFail() will throw a ModelNotFoundException.
            $user = User::where('email', $request->email)->firstOrFail();

            // Construct the reset password URL. This URL will be sent to the user's email.
            $url = URL::to('/users/reset-password/' . $user->id);

            // Prepare the data that will be passed to the ForgetPasswordMail Mailable class.
            $data = [
                'url' => $url,  // The reset password link
                'email' => $user->email,  // The user's email address
                'title' => 'Reset Password Link',  // The email title
                'body' => 'Please click on the below button to reset your password',  // The email body message
            ];

            // Send the reset password email to the user.
            // The ForgetPasswordMail class handles the email's structure and content.
            Mail::to($user->email)->send(new ForgetPasswordMail($data));

            // Return a success response if the email was sent successfully.
            return successResponse('Mail sent successfully. Please check your Inbox.');
        } catch (\Exception $e) {
            // If an exception is thrown, catch it and return an error response.
            // The error message from the exception is included in the response.
            return errorResponse('An error occurred: ' . $e->getMessage());
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
