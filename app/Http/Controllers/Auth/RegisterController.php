<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Traits\ApiResponseTrait;
use App\Traits\HandlesImageUploads;
use App\Models\User;

class RegisterController extends Controller
{
    use ApiResponseTrait;
    use HandlesImageUploads;
    /**
     * Register a new user to the database.
     * @param StoreAccountRequest $request
     */
    public function register(StoreAccountRequest $request)
    {
        try {
            // create a new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            // check if the user uploaded an image and save it in the storage folder
            if ($request->hasFile('image')) {
                $imageName = $this->storeImage($request->file('image'));
                $user->image()->create([
                    'url' => $imageName
                ]);
            }

            // create a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->createdResponse(['user' => $user, 'token' => $token]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
