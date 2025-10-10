<?php

namespace Modules\AdminUser\Service\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Order\App\Models\Order;
use Modules\Product\App\Models\Product;
use Modules\Product\App\Models\ProductOptionValue;
use Modules\Product\App\Models\ProductOptionVariant;
use Modules\Review\App\Models\Review;
use Modules\User\App\Models\User;

class DashboardService
{
    public function index($request)
    {
        $period = $request->get('period', 'daily');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $currentDateRange = $this->getDateRange($period, $startDate, $endDate);
        $previousDateRange = $this->getPreviousDateRange($period, $startDate, $endDate);

        $currentMetrics = $this->calculateMetrics($currentDateRange);
        $previousMetrics = $this->calculateMetrics($previousDateRange);

        return [
            'currentTotalRevenue' => $currentMetrics['totalRevenue'],
            'currentOrderCount' => $currentMetrics['orderCount'],
            'currentAvgOrderValue' => $currentMetrics['avgOrderValue'],
            'revenueChange' => $this->calculatePercentageChange($currentMetrics['totalRevenue'], $previousMetrics['totalRevenue']),
            'orderCountChange' => $this->calculatePercentageChange($currentMetrics['orderCount'], $previousMetrics['orderCount']),
            'avgOrderValueChange' => $this->calculatePercentageChange($currentMetrics['avgOrderValue'], $previousMetrics['avgOrderValue']),
            'totalUsers' => User::count(),
            'newUsers' => User::whereBetween('created_at', [$currentDateRange['start'], $currentDateRange['end']])->count(),
            'newCustomers' => $this->calculateNewCustomers($currentDateRange),
            'returningCustomers' => $this->calculateReturningCustomers($currentDateRange),
            'topSellingProducts' => $this->getTopSellingProducts($currentDateRange, 5),
            'lowStockProducts' => $this->getLowStockProducts(10),
            'mostSearchedKeywords' => $this->getTopSearchedKeywords(10),
            'recentOrders' => $this->getRecentOrders(7),
            'activityLogs' => $this->getAdminUserActivityLog(10),
            'productReviews' => $this->getProductReviews(10),
        ];
    }

    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay(),
            ];
        }

        $now = Carbon::now();
        switch ($period) {
            case 'daily':
                return ['start' => $now->copy()->startOfDay(), 'end' => $now->copy()->endOfDay()];
            case 'weekly':
                return ['start' => $now->copy()->startOfWeek(), 'end' => $now->copy()->endOfWeek()];
            case 'monthly':
                return ['start' => $now->copy()->startOfMonth(), 'end' => $now->copy()->endOfMonth()];
        }
    }

    private function getPreviousDateRange($period, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->subDays(Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)) + 1);
            $end = Carbon::parse($startDate)->subDay();
            return [
                'start' => $start->startOfDay(),
                'end' => $end->endOfDay(),
            ];
        }

        $now = Carbon::now();
        switch ($period) {
            case 'daily':
                return ['start' => $now->copy()->subDay()->startOfDay(), 'end' => $now->copy()->subDay()->endOfDay()];
            case 'weekly':
                return ['start' => $now->copy()->subWeek()->startOfWeek(), 'end' => $now->copy()->subWeek()->endOfWeek()];
            case 'monthly':
                return ['start' => $now->copy()->subMonth()->startOfMonth(), 'end' => $now->copy()->subMonth()->endOfMonth()];
        }
    }

    private function calculateMetrics($dateRange)
    {
        $orders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', Order::DELIVERED)->get();

        $totalRevenue = $orders->sum('total');
        $orderCount = $orders->count();
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        return [
            'totalRevenue' => $totalRevenue,
            'orderCount' => $orderCount,
            'avgOrderValue' => $avgOrderValue,
        ];
    }

    private function calculatePercentageChange($currentValue, $previousValue)
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }
        return (($currentValue - $previousValue) / $previousValue) * 100;
    }

    private function calculateNewCustomers($dateRange)
    {
        return User::whereHas('orders', function ($query) use ($dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        })->whereDoesntHave('orders', function ($query) use ($dateRange) {
            $query->where('created_at', '<', $dateRange['start']);
        })->count();
    }

    private function calculateReturningCustomers($dateRange)
    {
        return User::whereHas('orders', function ($query) use ($dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        })->whereHas('orders', function ($query) use ($dateRange) {
            $query->where('created_at', '<', $dateRange['start']);
        })->count();
    }

    private function getTopSellingProducts($dateRange, $limit = 5)
    {
        $topSellingProducts = Product::select(
            'products.id as product_id',
            'products.product_name',
            'products.uuid',
            'product_variants.id as variant_id',
            'product_variants.name as variant_name',
            DB::raw('SUM(order_items.quantity) as total_quantity')
        )
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '=', Order::DELIVERED)
            ->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('products.id', 'products.product_name', 'products.uuid', 'product_variants.id', 'product_variants.name')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit($limit)
            ->get();

        return $topSellingProducts->map(function ($product) {
            $baseImage = $this->getProductImage($product);
            return [
                'id' => $product->uuid,
                'productName' => $product->product_name,
                'variantName' => $product->variant_name ?? 'No Variant',
                'totalQuantity' => $product->total_quantity,
                'baseImage' => $baseImage
            ];
        });
    }

    private function getProductImage($product)
    {
        if (isset($product->variant_id)) {
            $productOptionValueId = ProductOptionVariant::where('product_id', $product->product_id)
                ->where('product_variant_id', $product->variant_id)
                ->first('product_option_value_id');
            $image = ProductOptionValue::find($productOptionValueId->product_option_value_id)
                ->filterFiles('baseImage')
                ->first();
        } else {
            $image = Product::find($product->product_id)
                ->filterFiles('baseImage')
                ->first();
        }
        return $image->path . '/Thumbnail/' . $image->temp_filename;
    }

    private function getLowStockProducts($threshold = 10)
    {
        $nonVariantProducts = Product::where('has_variant', false)
            ->where('quantity', '<=', $threshold)
            ->where('status', true)
            ->orderBy('quantity', 'asc')
            ->get();

        $variantProducts = Product::where('has_variant', true)
            ->where('status', true)
            ->with(['variants' => function ($query) use ($threshold) {
                $query->where('quantity', '<=', $threshold)
                    ->orderBy('quantity', 'asc');
            }])
            ->get()
            ->pluck('variants')
            ->flatten();

        return $nonVariantProducts->concat($variantProducts)
            ->map(function ($product) {
                // Get variant option details from pivot table relationship
                $variantDetails = 'No Variant';
                $variantColor = '';

                if ($product->optionValues && $product->optionValues->isNotEmpty()) {
                    $optionNames = $product->optionValues->map(function($optionValue) {
                        return $optionValue->option_name;
                    })->implode(' ');
                    $variantDetails = $optionNames ?: 'No Variant';

                    // Get the first option's data (usually color)
                    $firstOptionValue = $product->optionValues->first();
                    $variantColor = $firstOptionValue->option_data ?? '';
                }

                return [
                    'id' => $product->product->uuid ?? $product->uuid,
                    'productName' => $product->product_name ?? $product->product->product_name,
                    'variant' => $variantDetails,
                    'variantColor' => $variantColor,
                    'quantityLeft' => $product->quantity,
                ];
            });
    }

    private function getTopSearchedKeywords($limit = 10)
    {
        return DB::table('product_search_keywords')
            ->select('keyword', 'count')
            ->orderBy('count', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getRecentOrders($limit = 7)
    {
        return Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'orderId' => $order->id,
                    'customerName' => $order->user->full_name,
                    'status' => Order::$orderStatusMapping[$order->status],
                    'total' => $order->total,
                ];
            });
    }

    public function getAdminUserActivityLog($limit = 10)
    {
        try {
            $user = Auth::user();
            $query = DB::table('admin_user_activity_log')
                ->select('activityType', 'description', 'created_at as createdAt');

            if (!$user->hasRole('Super Admin')) {
                $query->where('modifierId', $user->id);
            }

            return $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($log) {
                    $log->createdAt = $this->formatDate($log->createdAt);
                    return $log;
                });
        } catch (\Exception $exception) {
            dd($exception);
        }

    }

    private function formatDate($date): string
    {
        $carbonDate = Carbon::parse($date);
        return $carbonDate->isToday()
            ? $carbonDate->diffForHumans()
            : $carbonDate->isoFormat('D MMMM YYYY, h:mm A');
    }

    public function getProductReviews($limit = 5)
    {
        return Review::with('user', 'product', 'replies')
            ->where('type', 'review')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->uuid,
                    'productName' => $review->product->product_name,
                    'customerName' => $review->name,
                    'profilePicture' => $review->user->profile_picture ?? null,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                ];
            });
    }
}
