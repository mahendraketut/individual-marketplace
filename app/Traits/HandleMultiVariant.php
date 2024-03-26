<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Variant;
use Illuminate\Http\Request;

trait HandleMultiVariant
{
    /**
     * handling multiple variant  of product.
     * @param array $variantData
     * @param integer $parentVariantID
     */

    // private function processVariants($variantsData, $parentVariantId, $product)
    // {
    //     foreach ($variantsData as $variantData) {
    //         $variant = Variant::create([
    //             'name' => $variantData['name'],
    //             'price' => $variantData['price'],
    //             'quantity' => $variantData['quantity'],
    //             'parent_id' => $parentVariantId,
    //         ]);

    //         $product->variants()->attach($variant->id);

    //         if (!empty($variantData['children'])) {
    //             $this->processVariants($variantData['children'], $variant->id, $product);
    //         }
    //     }
    // }

    private function createOrUpdateVariant($variantData, $parentVariantId = null, $product)
    {
        // Create or update the variant
        $variant = Variant::updateOrCreate(
            [
                'name' => $variantData['name'],
                'parent_id' => $parentVariantId,
                'product_id' => $product->id, // Pass product_id directly
            ],
            [
                'price' => $variantData['price'],
                'quantity' => $variantData['quantity']
            ]
        );

        // Process child variants recursively
        if (isset($variantData['children']) && is_array($variantData['children'])) {
            foreach ($variantData['children'] as $childVariantData) {
                $this->createOrUpdateVariant($childVariantData, $variant->id, $product);
            }
        }
    }
}
