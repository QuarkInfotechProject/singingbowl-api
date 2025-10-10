<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Host
    |--------------------------------------------------------------------------
    |
    | This is the master host used by Meilisearch. It should be a valid
    | URL with the port number.
    |
    */

    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),

    /*
    |--------------------------------------------------------------------------
    | Meilisearch API Key
    |--------------------------------------------------------------------------
    |
    | This is the master API key for your Meilisearch instance. It should be
    | an alphanumeric string.
    |
    */

    'key' => env('MEILISEARCH_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Index Settings
    |--------------------------------------------------------------------------
    |
    | Here you can define the default settings for your Meilisearch indexes.
    | You can override these settings on a per-model basis by implementing
    | the `meilisearchSettings()` method on your searchable models.
    |
    | For more information, please see Meilisearch's documentation:
    | https://docs.meilisearch.com/reference/api/settings.html
    |
    */

    'index-settings' => [
        // 'filterableAttributes' => ['*'],
        // 'sortableAttributes' => ['*'],
        // 'rankingRules' => [
        //     'words',
        //     'typo',
        //     'proximity',
        //     'attribute',
        //     'sort',
        //     'exactness',
        // ],
        // 'stopWords' => [],
        // 'synonyms' => [],
        // 'distinctAttribute' => null,
        // 'typoTolerance' => [
        //     'enabled' => true,
        //     'minWordSizeForTypos' => [
        //         'oneTypo' => 5,
        //         'twoTypos' => 9,
        //     ],
        //     'disableOnAttributes' => [],
        //     'disableOnWords' => [],
        // ],
        // 'faceting' => [
        //     'maxValuesPerFacet' => 100,
        // ],
        // 'pagination' => [
        //     'maxTotalHits' => 1000,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Meilisearch Model Specific Settings
    |--------------------------------------------------------------------------
    |
    | Here you can define model specific settings that will override the
    | default index settings.
    |
    */

    'model-settings' => [
        // App\Models\User::class => [
        //     'filterableAttributes' => ['id', 'email'],
        //     'sortableAttributes' => ['created_at'],
        // ],
    ],
];
