<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Traits\ApiResponseTrait;
use App\Traits\HandlesImageUploads;

class CategoryController extends Controller
{
    use ApiResponseTrait;
    use HandlesImageUploads;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // try to get the category
        try {
            // get all categories from the database
            $categories = Category::with('image')->get();
            // return a success response
            return $this->showResponse($categories);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        //try to create the category
        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '-', $request->name)),
                'description' => $request->description,
            ]);

            // check if the user uploaded an image
            if ($request->hasFile('image')) {
                // store the image in the storage folder using Handle Image Uploads trait
                $imageName = $this->storeImage($request->file('image'));
                // create a new image record in the database and associate it with the category
                $category->image()->create([
                    'url' => $imageName
                ]);
            }

            // return a success response
            return $this->successResponse($category, 'Category created successfully', 201);
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
        // try to get the category
        try {
            // get the category from the database
            $category = Category::where('slug', $slug)->with('image')->first();
            // return a success response
            return $this->showResponse($category);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $slug)
    {
        // try to update the category
        try {
            // find the category by slug
            $category = Category::where('slug', $slug)->firstOrFail();
            // update the category
            $category->update([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '-', $request->name)),
                'description' => $request->description,
            ]);

            // check if the user uploaded an image
            if ($request->hasFile('image')) {
                // update the image in the storage folder using Handle Image Uploads trait
                $imageName = $this->updateImage($request->file('image'), 'public/images', $category->image->url);
                // update the image record in the database
                $category->image()->update([
                    'url' => $imageName
                ]);
            }

            // return a success response
            return $this->successResponse($category, 'Category updated successfully', 200);
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
        // try to delete the category
        try {
            // find the category by slug
            $category = Category::where('slug', $slug)->firstOrFail();
            // delete the category
            $category->delete();
            // return a success response
            return $this->successResponse([], 'Category deleted successfully', 200);
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
        // try to get the trashed categories
        try {
            // get all trashed categories from the database
            $categories = Category::onlyTrashed()->with('image')->get();
            // return a success response
            return $this->showResponse($categories);
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
        // try to restore the category
        try {
            // find the category by slug
            $category = Category::withTrashed()->where('slug', $slug)->firstOrFail();
            // restore the category
            $category->restore();
            // return a success response
            return $this->successResponse($category, 'Category restored successfully', 200);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
