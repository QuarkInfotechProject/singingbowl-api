<?php

namespace Modules\Others\Service\LimitedTimeDeals\Admin;

use Modules\Others\App\Models\LimitedTimeDeals;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LimitedTimeDealExpirationService
{
    /**
     * Deactivate limited time deals that have expired
     *
     * @return array
     */
    public function deactivateExpiredDeals(): array
    {
        $now = Carbon::now();
        $deactivatedDeals = [];
        $count = 0;

        try {
            DB::beginTransaction();

            // Find deals that are active but have expired products
            $expiredDeals = LimitedTimeDeals::with(['product'])
                ->where('status', true)
                ->whereHas('product', function ($query) use ($now) {
                    $query->whereNotNull('special_price_end')
                          ->where('special_price_end', '<', $now);
                })
                ->get();

            foreach ($expiredDeals as $deal) {
                // Update deal status to inactive
                $deal->update(['status' => false]);

                $deactivatedDeals[] = [
                    'id' => $deal->id,
                    'product_name' => $deal->product->product_name,
                    'expired_at' => $deal->product->special_price_end->format('Y-m-d H:i:s')
                ];

                $count++;
            }

            DB::commit();

            return [
                'success' => true,
                'count' => $count,
                'deactivated_deals' => $deactivatedDeals,
                'message' => $count > 0
                    ? "Successfully deactivated {$count} expired deals."
                    : "No expired deals found."
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'count' => 0,
                'deactivated_deals' => [],
                'message' => 'Error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get expired deals without deactivating them (for preview)
     *
     * @return array
     */
    public function getExpiredDeals(): array
    {
        $now = Carbon::now();

        $expiredDeals = LimitedTimeDeals::with(['product'])
            ->where('status', true)
            ->whereHas('product', function ($query) use ($now) {
                $query->whereNotNull('special_price_end')
                      ->where('special_price_end', '<', $now);
            })
            ->get();

        return $expiredDeals->map(function ($deal) {
            return [
                'id' => $deal->id,
                'product_name' => $deal->product->product_name,
                'product_uuid' => $deal->product->uuid,
                'expired_at' => $deal->product->special_price_end->format('Y-m-d H:i:s'),
                'expired_since' => $deal->product->special_price_end->diffForHumans()
            ];
        })->toArray();
    }
}
