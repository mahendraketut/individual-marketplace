<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAccountRequest;
use App\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    use ApiResponseTrait;
    /**
     * Update the specified user in the database.
     * @param UpdateAccountRequest $request
     * @param User $user
     */
    public function update(UpdateAccountRequest $request, $id)
    {
        $user = User::find($id)->with('image')->first();

        if ($user == null) {
            return $this->notFoundResponse('User not found');
        } else {
            $user->update($request->except('image'));

            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::delete($user->image->url);
                    $user->image->delete();
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->extension();
                $path = $image->storeAs('images', $imageName, 'public');
                $user->image()->create([
                    'url' => $path
                ]);
            }
            return $this->successResponse($user, 'User updated successfully');
        }
    }
}
