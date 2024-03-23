<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use ApiResponseTrait;
    /**
     * Login the user to the application.
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->unauthorizedResponse('The provided credentials are incorrect');
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return $this->successResponse(['user' => $user, 'token' => $token], 'User logged in successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
