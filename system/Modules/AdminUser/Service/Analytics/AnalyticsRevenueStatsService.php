<?php

namespace Modules\AdminUser\Service\Analytics;

use Illuminate\Support\Facades\Log;

class AnalyticsRevenueStatsService extends BaseAnalyticsService
{
    public function index($data)
    {
        try {
            $dateRange = $this->getDateRange($data['filter']);
            $comparisonRange = $this->getComparisonRange($data['compareTo'], [
                'start' => $dateRange['start']->copy(),
                'end' => $dateRange['end']->copy()
            ]);

            return [
                'current' => $this->getChartData($dateRange),
                'comparison' => $this->getChartData($comparisonRange)
            ];
        } catch (\Exception $exception) {
            Log::error('Error fetching analytics for revenue stats: ' . $exception->getMessage());
            throw $exception;
        }
    }
}
