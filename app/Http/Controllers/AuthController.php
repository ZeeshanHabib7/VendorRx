<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserSignUpRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Interfaces\PaymentServiceInterface;

class AuthController extends Controller
{
    protected $paymentService;

    // Injected Service 
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function signUp(UserSignUpRequest $request) {
        try {
            // created model instance
            $user = new User();
            // Convert the request into an array
            $payload = $request->validated();
            // Create Customer on stripe
            $stripeCustomer = $this->paymentService->createCustomer([
                'name' => $payload['name'],
                'email' => $payload['email'],
            ]);
            // added stripe customer id in payload
            $payload["stripe_customer_id"] = $stripeCustomer->id;
            // calling create user function from model
            $user = $user->createNewUser($payload);
            // generate token function call
            $token = $this->generateToken($user);
            // data to be passed in resource file
            $data = $this->generateAuthorisedUser($token, $user);
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
            $token = $this->authenticateUser($credentials);
            // get authenticated user
            $user = getAuthenticatedUser();
            // data to be passed in resource file
            $data = $this->generateAuthorisedUser($token, $user);
            // Success response upon user login
            return successResponse(  "User Logged-in Sucessfully!",UserResource::make($data));
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function authenticateUser($credentials){
        return  auth('api')->attempt($credentials);
    }

    public function generateAuthorisedUser($token, $user) {
        return  [
            'token' => $token,
            'user' => $user
        ];
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
