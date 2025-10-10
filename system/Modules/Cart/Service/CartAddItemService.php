<?php

namespace Modules\Cart\Service;

use Modules\Cart\Service\CartRepository;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductVariant;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CartAddItemService
{
    public function __construct(private CartRepository $cartRepository)
    {
    }

    /**
     * Add item to cart based on cart type and identifier.
     */
    public function addItem(string $cartType, $cartIdentifier, array $products, $userAgent = null)
    {
        // Validate that we have valid products with ID and quantity
        $this->validateProducts($products);

        if ($cartType === 'user') {
            $cart = $this->addItemToUserCart($cartIdentifier, $products, $userAgent);
            // Removed cache invalidation for real-time consistency
            return [
                'cart_id' => $cart->uuid
            ];
        } else {
            $cart = $this->addItemToGuestCart($cartIdentifier, $products);
            // Removed cache invalidation for real-time consistency
            return [
                'cart_id' => $cart->guest_token
            ];
        }
    }

    /**
     * Add items to a user cart.
     */
    private function addItemToUserCart($userId, array $products, $userAgent)
    {
        // Get or create user cart
        $cart = $this->cartRepository->createOrGetUserCart($userId, $userAgent);

        // Process each product
        foreach ($products as $productData) {
            // Check if product is available
            $product = $this->validateProductAvailability($productData['id'], $productData['variant_id'] ?? null);

            // Prepare product data with price
            $productData['price'] = $this->getProductPrice($product, $productData['variant_id'] ?? null);

            // Get variant options if we have a variant
            if (isset($productData['variant_id']) && $productData['variant_id']) {
                $productData['variant_options'] = $this->getVariantOptions($productData['variant_id']);
            }

            $cart = $this->cartRepository->addItemToUserCart($cart, $productData);
        }

        return $cart;
    }

    /**
     * Add items to a guest cart.
     */
    private function addItemToGuestCart($guestToken, array $products)
    {
        // Get or create guest cart
        $guestCart = $this->cartRepository->createOrGetGuestCart($guestToken);

        // Process each product
        foreach ($products as $productData) {
            // Check if product is available
            $product = $this->validateProductAvailability($productData['id'], $productData['variant_id'] ?? null);

            // Prepare product data with price
            $productData['price'] = $this->getProductPrice($product, $productData['variant_id'] ?? null);

            // Get variant options if we have a variant
            if (isset($productData['variant_id']) && $productData['variant_id']) {
                $productData['variant_options'] = $this->getVariantOptions($productData['variant_id']);
            }

            $guestCart = $this->cartRepository->addItemToGuestCart($guestCart, $productData);
        }

        return $guestCart;
    }

    /**
     * Validate product array structure.
     */
    private function validateProducts(array $products)
    {
        if (empty($products)) {
            throw new Exception('No products provided.', ErrorCode::BAD_REQUEST);
        }

        foreach ($products as $product) {
            if (!isset($product['id']) || !isset($product['quantity'])) {
                throw new Exception('Each product must have id and quantity.', ErrorCode::BAD_REQUEST);
            }

            if ($product['quantity'] < 1) {
                throw new Exception('Product quantity must be at least 1.', ErrorCode::BAD_REQUEST);
            }
        }
    }

    /**
     * Validate that a product is available and exists.
     */
    private function validateProductAvailability($productId, $variantId = null)
    {
        $product = Product::with(['variants'])->find($productId);

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        if (isset($product->status) && $product->status === 0) {
            throw new Exception('Product is not available.', ErrorCode::BAD_REQUEST);
        }

        if ($variantId) {
            if (!$product->relationLoaded('variants') || $product->variants->isEmpty()) {
                $variant = ProductVariant::find($variantId);
                if (!$variant) {
                    throw new Exception('Product variant not found.', ErrorCode::NOT_FOUND);
                }

                // Check if variant is out of stock
                if (!$variant->in_stock) {
                    throw new Exception('Product variant is out of stock.', ErrorCode::BAD_REQUEST);
                }
            } else {
                $variant = $product->variants->where('id', $variantId)->first();

                if (!$variant) {
                    throw new Exception('Product variant not found.', ErrorCode::NOT_FOUND);
                }

                if (!$variant->in_stock) {
                    throw new Exception('Product variant is out of stock.', ErrorCode::BAD_REQUEST);
                }
            }
        } else {
            // Only check product stock when no variant is provided
            if (!$product->in_stock) {
                throw new Exception('Product is out of stock.', ErrorCode::BAD_REQUEST);
            }
        }

        return $product;
    }

    /**
     * Get price for a product or variant.
     * Uses special_price if available and valid, otherwise uses original_price.
     */
    private function getProductPrice($product, $variantId = null)
    {
        $source = $variantId ? ProductVariant::find($variantId) : $product;
        return $source ? $source->current_price : 0;
    }



    /**
     * Get variant options for a variant.
     */
    private function getVariantOptions($variantId)
    {
        $optionValues = ProductOptionValue::whereHas('variants', function ($query) use ($variantId) {
            $query->where('product_variant_id', $variantId);
        })->with('option')->get();

        if ($optionValues->isEmpty()) {
            return null;
        }

        $variantOptions = [];
        foreach ($optionValues as $optionValue) {
            if ($optionValue->option) {
                $variantOptions[] = [
                    'option_id' => $optionValue->product_option_id,
                    'option_name' => $optionValue->option->name,
                    'value_id' => $optionValue->id,
                    'value_name' => $optionValue->value ?? $optionValue->option_name,
                    'value_data' => $optionValue->data ?? $optionValue->option_data ?? '',
                    'is_color' => $optionValue->option->is_color ??
                                strtolower($optionValue->option->name) === 'color',
                ];
            }
        }

        return $variantOptions;
    }
}