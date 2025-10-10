<?php

return [
    'product_detail_ttl' => env('CACHE_PRODUCT_DETAIL_TTL', 6800), // 1 hour 53 minutes
    'product_index_ttl' => env('CACHE_PRODUCT_INDEX_TTL', 6000),    // 1 hour 40 minutes
    'search_results_ttl' => env('CACHE_SEARCH_RESULTS_TTL', 5900), // 1 hour 30 minutes
    'hot_new_products_ttl' => env('CACHE_HOT_NEW_PRODUCTS_TTL', 7200), // 2 hours
    'trending_categories_index_ttl' => env('CACHE_TRENDING_CATEGORIES_INDEX_TTL', 3600), // 1 hour
    'trending_categories_detail_ttl' => env('CACHE_TRENDING_CATEGORIES_DETAIL_TTL', 3600), // 1 hour
    'content_index_ttl' => env('CACHE_CONTENT_INDEX_TTL', 3600), // 1 hour
    'category_index_ttl' => env('CACHE_CATEGORY_INDEX_TTL', 3600), // 1 hour
];
