<?php

declare(strict_types=1);

namespace Modules\Shared\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Predis\Connection\ConnectionException;

class CacheService
{
    protected int $defaultTtl = 3600; // 1 hour in seconds

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key
     * @param array<string> $tags
     * @return mixed
     */
    public function get(string $key, array $tags = []): mixed
    {
        try {
            if (!empty($tags)) {
                return Cache::tags($tags)->get($key);
            }
            return Cache::get($key);
        } catch (ConnectionException $e) {
            Log::error("Redis connection error on GET: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl (in seconds)
     * @param array<string> $tags
     * @return bool
     */
    public function put(string $key, mixed $value, ?int $ttl = null, array $tags = []): bool
    {
        try {
            $ttl = $ttl ?? $this->defaultTtl;
            if (!empty($tags)) {
                return Cache::tags($tags)->put($key, $value, $ttl);
            }
            return Cache::put($key, $value, $ttl);
        } catch (ConnectionException $e) {
            Log::error("Redis connection error on PUT: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove an item from the cache by key.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (ConnectionException $e) {
            Log::error("Redis connection error on FORGET: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove items from the cache by tags.
     *
     * @param array<string> $tags
     * @return bool
     */
    public function forgetByTags(array $tags): bool
    {
        try {
            if (empty($tags)) {
                return false;
            }
            return Cache::tags($tags)->flush();
        } catch (ConnectionException $e) {
            Log::error("Redis connection error on FORGET_BY_TAGS: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get an item from the cache, or execute a closure and store the result.
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @param array<string> $tags
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null, array $tags = []): mixed
    {
        try {
            $value = $this->get($key, $tags);

            if ($value !== null) {
                return $value;
            }

            $value = $callback();
            $this->put($key, $value, $ttl, $tags);

            return $value;
        } catch (ConnectionException $e) {
            Log::error("Redis connection error on REMEMBER: " . $e->getMessage());
            // If cache fails, still execute the callback and return the result
            return $callback();
        }
    }

    /**
     * Generate a cache key.
     *
     * @param string $base
     * @param array<string, mixed> $params
     * @return string
     */
    public function generateKey(string $base, array $params = []): string
    {
        if (empty($params)) {
            return $base;
        }
        // Sort params by key to ensure consistent key generation
        ksort($params);
        return $base . ':' . md5(http_build_query($params));
    }

    public function generateProductKey(int|string $productId): string
    {
        return "product:{$productId}";
    }

    public function generateProductRelatedKey(int|string $productId): string
    {
        return "product:{$productId}:related";
    }

    public function generateProductSimilarKey(int|string $productId): string
    {
        return "product:{$productId}:similar";
    }

    public function generateProductVariantDescriptionKey(int|string $productId): string
    {
        return "product:{$productId}:variants";
    }

    /**
     * @param int $page
     * @param array<string, mixed> $filters
     * @return string
     */
    public function generateProductIndexKey(int $page, array $filters = []): string
    {
        return $this->generateKey("products:index:{$page}", $filters);
    }

    /**
     * @param string $query
     * @param array<string, mixed> $filters
     * @return string
     */
    public function generateSearchKey(string $query, array $filters = []): string
    {
        $params = array_merge(['q' => $query], $filters);
        return 'search:' . md5(http_build_query($params));
    }

    /**
     * Get the TTL for product details.
     *
     * @return int
     */
    public function getProductDetailTtl(): int
    {
        return config('cache_settings.product_detail_ttl', 4800); // 1 hour 20 minutes
    }

    /**
     * Get the TTL for product index pages.
     *
     * @return int
     */
    public function getProductIndexTtl(): int
    {
        return config('cache_settings.product_index_ttl', 6000); // 1 hour 40 minutes
    }

    /**
     * Get the TTL for search results.
     *
     * @return int
     */
    public function getSearchResultsTtl(): int
    {
        return config('cache_settings.search_results_ttl', 5900); // 1 hour 30 minutes
    }

    /**
     * Get the TTL for hot/new products (optional).
     *
     * @return int
     */
    public function getHotNewProductsTtl(): int
    {
        return config('cache_settings.hot_new_products_ttl', 7200); // 2 hours
    }

    /**
     * Get the TTL for trending categories index.
     *
     * @return int
     */
    public function getTrendingCategoriesIndexTtl(): int
    {
        return config('cache_settings.trending_categories_index_ttl', 3600); // 1 hour
    }

    /**
     * Get the TTL for trending categories detail.
     *
     * @return int
     */
    public function getTrendingCategoriesDetailTtl(): int
    {
        return config('cache_settings.trending_categories_detail_ttl', 3600); // 1 hour
    }

    /**
     * Get the TTL for category index.
     *
     * @return int
     */
    public function getCategoryIndexTtl(): int
    {
        return config('cache_settings.category_index_ttl', 3600); // 1 hour
    }

    /**
     * Generate cache key for trending categories index.
     *
     * @param array<string, mixed> $filters
     * @return string
     */
    public function getTrendingCategoriesIndexKey(array $filters = []): string
    {
        if (empty($filters)) {
            return 'trending-categories:index';
        }
        return 'trending-categories:index:' . md5(http_build_query($filters));
    }

    /**
     * Generate cache key for trending categories detail.
     *
     * @param int $id
     * @return string
     */
    public function getTrendingCategoriesDetailKey(int $id): string
    {
        return 'trending-category:' . $id . ':detail';
    }
}
