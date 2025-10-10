<?php
namespace Modules\FlashSale\Service\User;

use Carbon\Carbon;
use Modules\Color\App\Models\Color;
use Modules\FlashSale\App\Models\FlashSale;
use Illuminate\Support\Collection;

class FlashSaleIndexService
{
    private const REQUIRED_RELATIONS = [
        'flashSaleProducts.product.reviews',
        'flashSaleProducts.product.variants.optionValues.files',
        'flashSaleProducts.product.brand',
        'flashSaleProducts.product.files',
    ];

    private const FLASH_SALE_FIELDS = [
        'id',
        'campaign_name',
        'theme_color',
        'text_color',
        'start_date',
        'end_date'
    ];

    public function getAllFlashSales(): Collection
    {
        $now = Carbon::now();

        return FlashSale::query()
            ->with(self::REQUIRED_RELATIONS)
            ->select(self::FLASH_SALE_FIELDS)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get()
            ->map(fn ($flashSale) => $this->transformFlashSale($flashSale));
    }

    private function transformFlashSale(FlashSale $flashSale): array
    {
        $themeColorHex = Color::where('id', $flashSale->theme_color)->value('hex_code');
        $textColorHex = Color::where('id', $flashSale->text_color)->value('hex_code');
        return [
            'id' => $flashSale->id,
            'campaignName' => $flashSale->campaign_name,
            'themeColor' => $themeColorHex,
            'textColor' => $textColorHex,
            'startDate' => $flashSale->start_date,
            'endDate' => $flashSale->end_date,
            'products' => $this->transformProducts($flashSale->flashSaleProducts),
            'files' => $this->getMediaFiles($flashSale),
        ];
    }

    private function transformProducts(Collection $flashSaleProducts): Collection
    {
        return $flashSaleProducts->map(function ($flashSaleProduct) {
            $product = $flashSaleProduct->product;
            $reviews = $product->reviews;
            $productVariant = null;
            $baseImage = null;

            if ($product->has_variant) {
                $productVariant = $this->getFirstProductVariant($product);
                $baseImage = $this->getBaseImage($product, $productVariant);
            } else {
                $baseImage = $product->filterFiles('baseImage')->first();
            }

            return [
                'uuid' => $product->uuid,
                'name' => $product->product_name,
                'originalPrice' => $product->has_variant && $productVariant ? $productVariant->original_price : $product->original_price,
                'specialPrice' => $product->has_variant && $productVariant ? $productVariant->special_price : $product->special_price,
                'slug' => $product->slug,
                'sku' => $product->sku,
                'inStock' => $product->has_variant && $productVariant ? $productVariant->in_stock : $product->in_stock,
                'brandId' => $product->brand_id,
                'hasVariant' => $product->has_variant,
                'quantity' => $product->has_variant && $productVariant ? $productVariant->quantity : $product->quantity,
                'reviewCount' => $reviews->count(),
                'rating' => round($reviews->avg('rating')),
                'soldCount' => $product->total_completed_sold ?? 0,
                'baseImage' => $baseImage ? [
                    'url' => $baseImage->url,
                ] : null,
            ];
        });
    }

    private function getBaseImage($product, $variant): ?object
    {
        if ($variant) {
            $variantImage = $variant->optionValues->first()?->filterFiles('baseImage')->first();
            if ($variantImage) {
                return $variantImage;
            }
        }

        return $product->filterFiles('baseImage')->first();
    }

    private function getFirstProductVariant($product)
    {
        $productOptionId = $product->options()->where('name', 'Color')->value('id');

        return $product->variants()->with('optionValues.files')
            ->whereHas('optionValues', function ($query) use ($productOptionId) {
                $query->where('product_option_id', $productOptionId);
            })->first(['id', 'original_price', 'special_price', 'special_price_start', 'special_price_end', 'in_stock', 'quantity']);
    }

    private function getMediaFiles(FlashSale $flashSale): array
    {
        $mediaFiles = [];
        $fileTypes = ['desktopBanner', 'mobileBanner'];

        foreach ($fileTypes as $type) {
            $file = $flashSale->filterFiles($type)->first();
            if ($file) {
                $mediaFiles[$type] = [
                    'url' => $file->url,
                    'filename' => $file->filename,
                ];
            }
        }

        return $mediaFiles;
    }
}
