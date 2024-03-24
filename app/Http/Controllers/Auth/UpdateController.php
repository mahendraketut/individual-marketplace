<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAccountRequest;
use App\Traits\ApiResponseTrait;
use App\Traits\HandlesImageUploads;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    use ApiResponseTrait, HandlesImageUploads;
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
                // store the image in the storage folder using Handle Image Uploads trait
                $imageNames = $this->updateImages($request->file('image'), 'public/images', $user->image->url);
                // create a new image record in the database and associate it with the user
                if (count($imageNames) === 1) {
                    $user->image()->update(['url' => $imageNames[0]]);
                } else {
                    foreach ($imageNames as $imageName) {
                        $user->image()->update(['url' => $imageName]);
                    }
                }
            }
            return $this->successResponse($user, 'User updated successfully');
        }
    }
}
