<?php

namespace Modules\AdminUser\Trait;

use Carbon\Carbon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

trait AnalyticsDateRangeTrait
{
    function getDateRange($filter)
    {
        if (is_string($filter)) {
            switch ($filter) {
                case 'today':
                    $start = now()->startOfDay();
                    $end = now()->endOfDay();
                    break;

                case 'yesterday':
                    $start = now()->subDay()->startOfDay();
                    $end = now()->subDay()->endOfDay();
                    break;

                case 'week_to_date':
                    $start = now()->startOfWeek();
                    $end = now()->endOfDay();
                    break;

                case 'last_week':
                    $start = now()->subWeek()->startOfWeek();
                    $end = now()->subWeek()->endOfWeek();
                    break;

                case 'month_to_date':
                    $start = now()->startOfMonth();
                    $end = now()->endOfDay();
                    break;

                case 'last_month':
                    $start = now()->subMonth()->startOfMonth();
                    $end = now()->subMonth()->endOfMonth();
                    break;

                case 'quarter_to_date':
                    $start = now()->firstOfQuarter();
                    $end = now()->endOfDay();
                    break;

                case 'last_quarter':
                    $start = now()->subQuarter()->firstOfQuarter();
                    $end = now()->subQuarter()->lastOfQuarter();
                    break;

                case 'year_to_date':
                    $start = now()->startOfYear();
                    $end = now()->endOfDay();
                    break;

                case 'last_year':
                    $start = now()->subYear()->startOfYear();
                    $end = now()->subYear()->endOfYear();
                    break;
                default:
                    throw new \Exception('Invalid filter provided.');
            }
        } elseif (is_array($filter)) {
            $start = Carbon::parse($filter['startDate'])->startOfDay();
            $end = Carbon::parse($filter['endDate'])->endOfDay();
        } else {
            throw new Exception('Invalid filter format.', ErrorCode::BAD_REQUEST);
        }

        return ['start' => $start, 'end' => $end];
    }

    function getComparisonRange($filter, $dateRange)
    {
        $diff = $dateRange['start']->diffInDays($dateRange['end']);

        if ($filter === 'previous_period') {
            $start = $dateRange['start']->subDays($diff + 1);
            $end = $dateRange['end']->subDays($diff + 1);
        } elseif ($filter === 'previous_year') {
            $start = $dateRange['start']->subYear();
            $end = $dateRange['end']->subYear();
        }

        return ['start' => $start, 'end' => $end];
    }

    function getCurrentDateRange($startDate, $endDate)
    {
        if ($startDate->isSameDay($endDate)) {
            return $startDate->format('M d, Y');
        }

        if ($startDate->format('M') === $endDate->format('M')) {
            return sprintf(
                '%s %d - %d, %s',
                $startDate->format('M'),
                $startDate->day,
                $endDate->day,
                $endDate->format('Y')
            );
        }

        return sprintf(
            '%s %d - %s %d, %s',
            $startDate->format('M'),
            $startDate->day,
            $endDate->format('M'),
            $endDate->day,
            $endDate->format('Y')
        );
    }

    function getPreviousDateRange($startDate, $endDate)
    {
        return $this->getCurrentDateRange($startDate, $endDate);
    }
}
