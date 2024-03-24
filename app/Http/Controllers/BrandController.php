<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Traits\ApiResponseTrait;
use App\Traits\HandlesImageUploads;


class BrandController extends Controller
{
    use ApiResponseTrait, HandlesImageUploads;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // get all brands from database by paginate the data
            $brands = Brand::all();
            // return a success response
            return $this->showResponse($brands);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        //Try to store the brand
        try {
            //make a brand instance
            $brand = Brand::create([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '-', $request->name)),
                'description' => $request->description,
            ]);

            // check if the user uploaded an image
            if ($request->hasFile('image')) {
                // store the image in the storage folder using Handle Image Uploads trait
                $imageNames = $this->storeImage($request->file('image'));
                // create a new image record in the database and associate it with the brand
                foreach ($imageNames as $imageName) {
                    $brand->image()->create(['url' => $imageName]);
                }
            }

            // return a success response
            return $this->createdResponse($brand);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        // Try to get the brand
        try {
            // find the brand by slug
            $brand = Brand::where('slug', $slug)->with('image')->firstOrFail();
            // return a success response
            return $this->showResponse($brand);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, $slug)
    {
        //try to update the brand
        try {
            // find the brand by slug
            $brand = Brand::where('slug', $slug)->firstOrFail();
            // update the brand
            $brand->update([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '-', $request->name)),
                'description' => $request->description,
            ]);

            // check if the user uploaded an image
            if ($request->hasFile('image')) {
                // store the image in the storage folder using Handle Image Uploads trait
                $imageNames = $this->updateImage($request->file('image'), 'public/images', $brand->image->url);
                // update the image record in the database
                foreach ($imageNames as $imageName) {
                    $brand->image()->update(['url' => $imageName]);
                }
            }

            // return a success response
            return $this->updateResponse($brand);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        // Try to delete the brand
        try {
            // delete the brand
            Brand::where('slug', $slug)->delete();
            // return a success response
            return $this->successResponse([], 'Brand deleted successfully', 200);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($slug)
    {
        // Try to restore the brand
        try {
            // find the brand by slug
            $brand = Brand::withTrashed()->where('slug', $slug)->firstOrFail();
            // restore the brand
            $brand->restore();
            // return a success response
            return $this->successResponse($brand, 'Brand restored successfully', 200);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        // Try to get all trashed brands
        try {
            // get all trashed brands
            $brands = Brand::onlyTrashed()->get();
            // return a success response
            return $this->showResponse($brands);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
