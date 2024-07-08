<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource_SA;
use App\Http\Requests\UserSearchRequest;
use App\Http\Requests\UserRegisterRequest_SA;
use App\Http\Requests\UserLoginRequest_SA;

class UserController extends Controller
{
    private $noOfRecordPerPage = 10;
    private $paginate = false;

    public function getAllUsers(UserSearchRequest $request)
    {
        try {
            $input = $request->only('search_value', 'search_by', 'page', 'pagination', 'perPage', 'type', 'id');
            $users = User::latest();

            // Check if pagination is requested
            if (isset($input['pagination']) && !empty($input['pagination'])) {
                $noOfRecordPerPage = $request->input('perPage', $this->noOfRecordPerPage); // Default to 10 records per page if not specified
                $this->paginate = true;

                // Perform pagination and format result as a resource collection
                $result = UserResource_SA::collection($users->paginate($noOfRecordPerPage));
            } elseif (isset($input['id']) && !empty($input['id'])) {
                // Retrieve a specific user by ID and format result as a single resource
                $result = UserResource_SA::make($users->findOrFail($input['id']));
            } else {
                // Retrieve all users and format result as a resource collection
                $result = UserResource_SA::collection($users->get());
            }

            // Return success response with the formatted result
            return successResponse('Records Fetched Successfully.', $result, $this->paginate);
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    public function register(UserRegisterRequest_SA $request)
    {
        $user = User::create([
            'name' =>$request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)

        ]);

        return userResponse("User Registered Sucessfully!", UserResource_SA::make($user));
    }

    public function Login(UserLoginRequest_SA $request)
    {

        if (!$token = auth()->attempt($request->all())) {
            return userResponse("Unauthenticated User", false, 404);
        }

        return $this->getToken($token);
    }

    protected function getToken($token)
    {
        return response()->json([
            'acess_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
