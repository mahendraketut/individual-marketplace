<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\ApiResponseTrait;
use App\Traits\HandlesImageUploads;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait, HandlesImageUploads;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $products = Product::with('images', 'category', 'brand', 'user', 'variantOptions');

            // check if the user is authenticated
            if (auth("sanctum")->check()) {
                // get all product that associated with the authenticated user
                $products = $products->where('user_id', auth("sanctum")->id());
            }

            // check if the user is searching for a product
            if ($request->has('search')) {
                $products = $products->where('name', 'like', '%' . $request->search . '%');
            }

            // check if the user is filtering by category
            if ($request->has('category')) {
                $products = $products->where('category_id', $request->category);
            }

            // check if the user is filtering by brand
            if ($request->has('brand')) {
                $products = $products->where('brand_id', $request->brand);
            }

            // check if the user is filtering by price range
            if ($request->has('min_price') && $request->has('max_price')) {
                $products = $products->whereBetween('price', [$request->min_price, $request->max_price]);
            }

            // check if the user is sorting the products by name, and price in ascending or descending
            if ($request->has('sort_by')) {
                $sortOrder = $request->has('sort_order') ? $request->sort_order : 'asc';
                $products = $products->orderBy($request->sort_by, $sortOrder);
            }

            // paginate the products
            $products = $products->paginate(10);
            // return a success response
            return $this->showResponse($products);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            // create a new product
            $product = Product::create([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '-', $request->name)),
                'description' => $request->description,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'user_id' => auth()->id()
            ]);

            // check if the user uploaded an image
            if ($request->hasFile('images')) {
                // store the image in the storage folder using Handle Image Uploads trait
                $imageNames = $this->storeImage($request->file('images'));
                // create a new image record in the database and associate it with the product
                if (count($imageNames) === 1) {
                    $product->images()->create(['url' => $imageNames[0]]);
                } else {
                    foreach ($imageNames as $imageName) {
                        $product->images()->create(['url' => $imageName]);
                    }
                }
            }

            // Check if the user is adding multiple variant options
            if ($request->has('variant_options')) {
                foreach ($request->variant_options as $variantName => $variantValues) {
                    foreach ($variantValues as $variantValue) {
                        $product->variantOptions()->create([
                            'name_variant' => $variantName,
                            'value_variant' => $variantValue,
                        ]);
                    }
                }
            }

            // return a success response
            return $this->createdResponse($product);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($slug)
    {
        try {
            //check if the user is authenticated
            if (auth()->check()) {
                // get the product that associated with the authenticated user
                $product = Product::where('user_id', auth()->id())->where('slug', $slug)->with('images', 'category', 'brand', 'user', 'variantOptions')->firstOrFail();
            } else {
                //get the product by slug without checking the user
                $product = Product::where('slug', $slug)->with('images', 'category', 'brand', 'user')->firstOrFail();
            }
            // return a success response
            return $this->showResponse($product);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $slug)
    {
        try {
            $product = Product::where('slug', $slug)->firstOrFail();

            // update the product
            $product->update([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '-', $request->name)),
                'description' => $request->description,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'user_id' => auth()->id()
            ]);

            // check if the user uploaded an image
            if ($request->hasFile('images')) {
                // store the image in the storage folder using Handle Image Uploads trait
                $imageNames = $this->updateImages($request->file('images'), $product->images->pluck('url')->toArray());
                // create a new image record in the database and associate it with the product
                if (count($imageNames) === 1) {
                    $product->images()->update(['url' => $imageNames[0]]);
                } else {
                    foreach ($imageNames as $imageName) {
                        $product->images()->update(['url' => $imageName]);
                    }
                }
            }

            // check if the user is adding variant options
            if ($request->has('variant_options')) {
                // delete all the variant options associated with the product
                $product->variantOptions()->delete();
                // create new variant options
                foreach ($request->variant_options as $variantName => $variantValues) {
                    foreach ($variantValues as $variantValue) {
                        $product->variantOptions()->create([
                            'name_variant' => $variantName,
                            'value_variant' => $variantValue,
                        ]);
                    }
                }
            }

            // return a success response
            return $this->showResponse($product);
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
        try {
            // find the product by slug
            $product = Product::where('slug', $slug)->firstOrFail();
            // delete the product
            $product->delete();
            // return a success response
            return $this->deleteResponse($product, 'Product deleted successfully');
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get the trashed resources.
     */
    public function trashed()
    {
        try {
            // get all trashed products
            $products = Product::onlyTrashed()->with('images', 'category', 'brand', 'user', 'variantOptions')->paginate(10);
            // return a success response
            return $this->showResponse($products);
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
        try {
            // find the product by slug
            $product = Product::withTrashed()->where('slug', $slug)->firstOrFail();
            // restore the product
            $product->restore();
            // return a success response
            return $this->successResponse($product, 'Product restored successfully', 200);
        } catch (\Exception $e) {
            // return an error response
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
