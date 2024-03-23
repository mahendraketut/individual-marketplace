<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiResponseTrait;
    /**
     * Logout the user from the application.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse([], 'User logged out successfully');
    }
}
