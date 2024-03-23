<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesImageUploads
{
    /**
     * Store the uploaded image and return the file name.
     *
     * @param  UploadedFile  $file
     * @param  string  $destination
     * @return string
     */
    protected function storeImage(UploadedFile $file, string $destination = 'public/images'): string
    {
        $imageName = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs($destination, $imageName);

        return $imageName;
    }

    /**
     * Update the uploaded image and return the file name.
     */
    protected function updateImage(UploadedFile $file, string $destination = 'public/images', string  $oldFileName = null): string
    {
        if ($oldFileName) {
            Storage::delete($destination . '/' . $oldFileName);
        }

        return $this->storeImage($file, $destination);
    }
}
