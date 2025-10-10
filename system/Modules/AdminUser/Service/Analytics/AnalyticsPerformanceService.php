<?php

namespace Modules\AdminUser\Service\Analytics;

use Illuminate\Support\Facades\Log;

class AnalyticsPerformanceService extends BaseAnalyticsService
{
    public function index($data)
    {
        try {
            $dateRange = $this->getDateRange($data['filter']);
            $comparisonRange = $this->getComparisonRange($data['compareTo'], [
                'start' => $dateRange['start']->copy(),
                'end' => $dateRange['end']->copy()
            ]);

            $currentDateRange = $this->getCurrentDateRange($dateRange['start'], $dateRange['end']);
            $previousDateRange = $this->getPreviousDateRange($comparisonRange['start'], $comparisonRange['end']);

            return [
                'current' => [
                    'current' => $currentDateRange,
                    'sales' => $this->getBasicOrderStats($dateRange)
                ],
                'comparison' => [
                    'comparison' => $previousDateRange,
                    'sales' => $this->getBasicOrderStats($comparisonRange)
                ]
            ];
        } catch (\Exception $exception) {
            Log::error('Error fetching analytics for performance: ' . $exception->getMessage());
            throw $exception;
        }
    }
}
