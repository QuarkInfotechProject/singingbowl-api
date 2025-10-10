<?php
namespace Modules\FlashSale\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\FlashSale\App\Models\FlashSale;
use Modules\Product\App\Models\Product;

class FlashSaleUpdateService
{
    private const FILLABLE_FIELDS = [
        'campaign_name',
        'start_date',
        'end_date',
        'theme_color',
        'text_color'
    ];
    public function update(array $data, string $ipAddress)
    {
        return DB::transaction(function () use ($data) {
            $flashSale = FlashSale::findOrFail($data['id']);

            $startDate = $data['start_date'] ?? $flashSale->start_date;
            $endDate = $data['end_date'] ?? $flashSale->end_date;

            $this->validateDateOverlap($startDate, $endDate, $flashSale->id);

            $this->updateFlashSaleProperties($flashSale, $data);

            $this->syncProducts($flashSale, $data);

            return $flashSale;
        });
    }
    private function updateFlashSaleProperties(FlashSale $flashSale, array $data): void
    {
        $flashSale->fill(array_filter(
            $data,
            fn($key) => in_array($key, self::FILLABLE_FIELDS),
            ARRAY_FILTER_USE_KEY
        ));

        if (isset($data['desktopBanner'])) {
            $flashSale->desktop_banner_id = $data['desktopBanner'];
        }

        if (isset($data['mobileBanner'])) {
            $flashSale->mobile_banner_id = $data['mobileBanner'];
        }

        $flashSale->save();
    }

    private function syncProducts(FlashSale $flashSale, array $data): void
    {
        $productIds = $data['product_id'] ?? $data['products'] ?? null;

        if (!empty($productIds) && is_array($productIds)) {
            $validProductIds = Product::whereIn('uuid', $productIds)
                ->pluck('id')
                ->toArray();

            $flashSale->products()->sync(
                array_fill_keys($validProductIds, [])
            );
        }
    }
    protected function validateDateOverlap(string $startDate, string $endDate, int $flashSaleId): void
    {
        $existingOverlap = FlashSale::where('id', '!=', $flashSaleId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->orWhere(function ($q) use ($startDate) {
                    $q->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $startDate);
                });

                $query->orWhere(function ($q) use ($endDate) {
                    $q->where('start_date', '<=', $endDate)
                      ->where('end_date', '>=', $endDate);
                });

                $query->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '>=', $startDate)
                      ->where('end_date', '<=', $endDate);
                });
            })
            ->exists();

        if ($existingOverlap) {
            throw ValidationException::withMessages([
                'start_date' => 'A flash sale campaign already exists during this time period.'
            ]);
        }
    }
}