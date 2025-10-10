<?php

namespace Modules\Product\Service\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Attribute\App\Models\AttributeValue;
use Modules\Media\App\Models\File;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductOption;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Product\App\Models\ProductVariant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;


class ProductShowService
{
    public function show(string $uuid)
    {
        $product = $this->getProduct($uuid);

        $categories = $this->getProductCategories($product);
        $mediaFiles = $this->getMediaFiles($product);
        $keySpecsData = $this->getKeySpecsData($product);
        $specificationData = $this->getSpecificationsData($product);
        $optionsData = $this->getOptionsData($product);
        $variants = $this->getVariants($product);
        $metaData = $this->getMetaData($product);
        $attributesData = $this->getAttributesData($product);
        $couponsData = $this->getCouponsData($product);
        $featuresData = $this->getFeaturesData($product);
        $activeOffersData = $this->getActiveOffersData($product);
        $relatedProductData = $this->getRelatedProductData($product);
        $upSellProductData = $this->getUpSellProductData($product);
        $crossSellProductData = $this->getCrossSellProductData($product);



        return [
            'productName' => $product->productName,
            'brandId' => $product->brandId,
            'url' => $product->url,
            'sortOrder' => $product->sortOrder,
            'bestSeller' => $product->bestSeller,
            'hasVariant' => $product->hasVariant,
            'originalPrice' => $product->originalPrice,
            'specialPrice' => $product->specialPrice,
            'specialPriceStart' => $product->specialPriceStart,
            'specialPriceEnd' => $product->specialPriceEnd,
            'sku' => $product->sku,
            'description' => $product->description,
            'additionalDescription' => $product->additionalDescription,
            'status' => $product->status,
            'saleStart' => $product->saleStart,
            'saleEnd' => $product->saleEnd,
            'quantity' => $product->quantity,
            'inStock' => $product->inStock,
            'newFrom' => $product->newFrom ?? '',
            'newTo' => $product->newTo ?? '',
            'categories' => $categories,
            'files' => $mediaFiles,
            'keySpecs' => $keySpecsData,
            'specifications' => $specificationData,
            'options' => $optionsData,
            'variants' => $variants,
            'meta' => $metaData,
            'attributes' => $attributesData,
            'coupons' => $couponsData,
            'activeOffers' => $activeOffersData,
            'others' => $featuresData,
            'relatedProducts' => $relatedProductData,
            'upSellProducts' => $upSellProductData,
            'crossSellProducts' => $crossSellProductData,
        ];
    }

    private function getProduct(string $uuid)
    {
        $product = Product::select(
            'id',
            'uuid',
            'product_name as productName',
            'brand_id as brandId',
            'slug as url',
            'sort_order as sortOrder',
            'best_seller as bestSeller',
            'has_variant as hasVariant',
            'original_price as originalPrice',
            'special_price as specialPrice',
            'special_price_start as specialPriceStart',
            'special_price_end as specialPriceEnd',
            'sku',
            'description',
            'additional_description as additionalDescription',
            'status',
            'sale_start as saleStart',
            'sale_end as saleEnd',
            'quantity',
            'in_stock as inStock',
            'new_from as newFrom',
            'new_to as newTo',
            'specifications'

        )->where('uuid', $uuid)->first();

        if (!$product) {
            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
        }

        return $product;
    }



    private function getProductCategories($product)
    {
        $categoryData = $product->categories()->get();
        $categories = [];

        foreach ($categoryData as $category) {
            $categories[] = [
                'id' => $category->id,
                'name' => $category->name,
            ];
        }

        return $categories;
    }
    private function getKeySpecsData($product)
    {
        $keySpecs = $product->keySpecs()
            ->select('id', 'spec_key as key', 'spec_value as value')
            ->get();

        return $keySpecs->map(function ($spec) {
            if (is_string($spec->value)) {
                $spec->value = json_decode($spec->value, true);
            }
            return $spec;
        })->toArray();
    }


    private function getMediaFiles($product)
    {
        $allFiles = $product->files()->get()->groupBy('pivot.zone');

        $baseImage = $allFiles->get('baseImage', collect())->map(function ($file) {
            return [
                'id' => $file->id,
                'baseImageUrl' => $file->path . '/Thumbnail/' . $file->temp_filename,
            ];
        })->first();

        $baseImage = $baseImage ?? '';

        $additionalImage = $allFiles->get('additionalImage', collect())->map(function ($file) {
            return [
                'id' => $file->id,
                'additionalImageUrl' => $file->path . '/Thumbnail/' . $file->temp_filename,
            ];
        })->all();

        $descriptionVideo = $allFiles->get('descriptionVideo', collect())->map(function ($file) {
            return [
                'id' => $file->id,
                'descriptionImageUrl' => $file->path . '/' . $file->temp_filename,
            ];
        })->all();

        return [
            'baseImage' => $baseImage,
            'additionalImage' => $additionalImage,
            'descriptionVideo' => $descriptionVideo,
        ];
    }

    private function getSpecificationsData($product)
    {
        return $product->specifications;
    }

    private function getOptionsData($product)
    {
        $options = ProductOption::select('id', 'uuid', 'name', 'has_image')
            ->where('product_id', $product->id)
            ->get();

        $optionsData = [];

        foreach ($options as $option) {
            $optionValuesData = [];

            $optionValues = ProductOptionValue::select('id', 'uuid', 'option_data as optionData', 'option_name as optionName')
                ->where('product_option_id', $option->id)
                ->get();

            foreach ($optionValues as $value) {
                $files = ['baseImage' => '', 'additionalImage' => []];

                if ($option->has_image) {
                    $allFiles = $value->files->groupBy('pivot.zone');

                    $baseImage = $allFiles->get('baseImage', collect())->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'url' => $file->path . '/Thumbnail/' . $file->temp_filename,
                        ];
                    })->first();

                    if ($baseImage) {
                        $files['baseImage'] = $baseImage;
                    }

                    $additionalImages = $allFiles->get('additionalImage', collect())->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'url' => $file->path . '/Thumbnail/' . $file->temp_filename,
                        ];
                    })->all();

                    $files['additionalImage'] = $additionalImages;

                    $optionValueData = [
                        'id' => $value->id,
                        'optionName' => $value->optionName,
                        'files' => $files,
                    ];
                } else {
                    $optionValueData = [
                        'id' => $value->id,
                        'optionName' => $value->optionName,
                    ];
                }

                $optionValuesData[] = $optionValueData;
            }

            $optionData = [
                'id' => $option->id,
                'name' => $option->name,
                'hasImage' => $option->has_image,
                'values' => $optionValuesData,
            ];

            $optionsData[] = $optionData;
        }

        return $optionsData;
    }

    private function getVariants($product)
    {
        $variantData = [];

        $variants =  ProductVariant::select('id',
            'name',
            'sku',
            'status',
            'original_price',
            'special_price',
            'special_price_start',
            'special_price_end',
            'quantity',
            'in_stock')
            ->where('product_id', $product->id)
            ->get();

        foreach ($variants as $variant) {
            $variantData[] = [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'status' => $variant->status,
                'originalPrice' => $variant->original_price,
                'specialPrice' => $variant->special_price ?? '',
                'specialPriceStart' => $variant->special_price_start ?? '',
                'specialPriceEnd' => $variant->special_price_end ?? '',
                'quantity' => $variant->quantity,
                'inStock' => $variant->in_stock,
            ];
        }

        return $variantData;
    }

    private function getMetaData($product)
    {
        $productMetaData = $product->meta()->first();

        return [
            'metaTitle' => $productMetaData['meta_title'],
            'keywords' => json_decode($productMetaData['meta_keywords']),
            'metaDescription' => $productMetaData['meta_description'],
        ];
    }

    private function getAttributesData($product)
    {
        $attributes = $product->attributes;

        $attributesData = [];

        foreach ($attributes as $attribute) {
            $id = $attribute->id;
            $attributeId = $attribute->attribute_id;
            $attributeName = $attribute->attribute->name;

            $values = [];
            foreach ($attribute->values as $value) {

                $productAttributeValue = AttributeValue::find($value->attribute_value_id);

                if ($productAttributeValue) {
                    $valueId = $productAttributeValue->id;
                    $valueName = $productAttributeValue->value;
                    $values[] = [
                        'id' => $valueId,
                        'name' => $valueName,
                    ];
                }
            }

            $attributeData = [
                'id' => $id,
                'attributeId' => $attributeId,
                'name' => $attributeName,
                'values' => $values,
            ];

            $attributesData[] = $attributeData;
        }

        return $attributesData;
    }

    private function getCouponsData(Product $product)
    {
        return DB::table('coupons')
            ->join('product_coupons', 'coupons.id', '=', 'product_coupons.coupon_id')
            ->where('product_coupons.product_id', $product->id)
            ->select('coupons.id', 'coupons.code')
            ->get();
    }

    private function getActiveOffersData(Product $product)
    {
        $activeOffers = $product->activeOffers()->get();
        $activeOffersData = [];

        foreach ($activeOffers as $activeOffer) {
            $activeOfferImage = $activeOffer->filterfiles('image')
                ->select(DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS image"))
                ->first();

            $activeOffersData[] = [
                'id' => $activeOffer->id,
                'text' => $activeOffer->text,
                'icon' => $activeOfferImage ? $activeOfferImage->image : null,
            ];
        }

        return $activeOffersData;
    }

    private function getFeaturesData(Product $product)
    {
        $features = $product->features()->get();
        $featuresData = [];

        foreach ($features as $feature) {

            $featuresData[] = [
                'id' => $feature->id,
                'text' => $feature->text,
            ];
        }

        return $featuresData;
    }

    private function getRelatedProductData($product)
    {
        $relatedProducts = $product->relatedProducts()->get();
        $relatedProductData = [];

        foreach ($relatedProducts as $relatedProduct) {
            $relatedProductImage = $relatedProduct->filterfiles('baseImage')
                ->select(DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS baseImage"))
                ->first();

            $relatedProductData[] = [
                'id' => $relatedProduct->id,
                'name' => $relatedProduct->product_name,
                'originalPrice' => $relatedProduct->original_price,
                'specialPrice' => $relatedProduct->special_price,
                'status' => $relatedProduct->status,
                'baseImage' => $relatedProductImage['baseImage'] ?? '',
            ];
        }

        return $relatedProductData;
    }

    private function getUpSellProductData($product)
    {
        $upSellProducts = $product->upSellProducts()->get();
        $upSellProductData = [];

        foreach ($upSellProducts as $upSellProduct) {
            $upSellProductImage = $upSellProduct->filterfiles('baseImage')
                ->select(DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS baseImage"))
                ->first();

            $upSellProductData[] = [
                'uuid' => $upSellProduct->uuid,
                'name' => $upSellProduct->product_name,
                'originalPrice' => $upSellProduct->original_price,
                'specialPrice' => $upSellProduct->special_price,
                'status' => $upSellProduct->status,
                'baseImage' => $upSellProductImage['baseImage'] ?? '',
            ];
        }

        return $upSellProductData;
    }

    private function getCrossSellProductData($product)
    {
        $crossSellProducts = $product->crossSellProducts()->get();
        $crossSellProductData = [];

        foreach ($crossSellProducts as $crossSellProduct) {
            $crossSellProductImage = $crossSellProduct->filterfiles('baseImage')
                ->select(DB::raw("CONCAT(path, '/Thumbnail/', temp_filename) AS baseImage"))
                ->first();

            $crossSellProductData[] = [
                'uuid' => $crossSellProduct->uuid,
                'name' => $crossSellProduct->product_name,
                'originalPrice' => $crossSellProduct->original_price,
                'specialPrice' => $crossSellProduct->special_price,
                'status' => $crossSellProduct->status,
                'baseImage' => $crossSellProductImage['baseImage'] ?? '',
            ];
        }

        return $crossSellProductData;
    }
}
