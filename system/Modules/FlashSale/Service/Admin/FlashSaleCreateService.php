<?php
namespace Modules\FlashSale\Service\Admin;

use Illuminate\Support\Facades\DB;
use Modules\FlashSale\App\Models\FlashSale;
use Modules\Product\App\Models\Product;
use Illuminate\Validation\ValidationException;

class FlashSaleCreateService
{
    public function create(array $data, string $ipAddress)
    {
        $this->validateDateOverlap($data['start_date'], $data['end_date']);

        try {
            DB::beginTransaction();

            $flashSale = FlashSale::create([
                'campaign_name' => $data['campaign_name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'theme_color' => $data['theme_color'],
                'text_color' => $data['text_color'],
                'desktop_banner_id' => $data['desktopBanner'] ?? null,
                'mobile_banner_id' => $data['mobileBanner'] ?? null,
            ]);

            if (!empty($data['product_id']) && is_array($data['product_id'])) {
                $validProductIds = Product::whereIn('uuid', $data['product_id'])->pluck('id')->toArray();
                $flashSale->products()->sync(array_fill_keys($validProductIds, []));
            }

            DB::commit();
            return $flashSale;

        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
    protected function validateDateOverlap(string $startDate, string $endDate): void
    {
        if (empty($endDate)) {
            return;
        }

        $existingOverlap = FlashSale::where(function ($query) use ($startDate, $endDate) {
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
        })->exists();

        if ($existingOverlap) {
            throw ValidationException::withMessages([
                'start_date' => 'A flash sale campaign already exists during this time period.'
            ]);
        }
    }
}