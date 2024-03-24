<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use App\Http\Requests\StoreWishlistRequest;
use App\Http\Requests\UpdateWishlistRequest;
use App\Traits\ApiResponseTrait;

class WishlistController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $wishlists = Wishlist::where('user_id', auth()->id())->with('product')->get();
            return $this->successResponse($wishlists, 'Wishlist retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * add product to wishlist
     */
    public function store(StoreWishlistRequest $request)
    {
        try {
            $wishlist = Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            return $this->successResponse($wishlist, 'Product added to wishlist successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove product from wishlist
     */
    public function destroy($id)
    {
        try {
            $wishlist = Wishlist::where('user_id', auth()->id())->where('product_id', $id)->firstOrFail();
            if ($wishlist) {
                $wishlist->delete();
                return $this->successResponse([], 'Product removed from wishlist successfully', 200);
            }
            return $this->errorResponse('Product not found in wishlist', 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
