<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client as MeilisearchClient;
use Meilisearch\Exceptions\ApiException;
use Modules\Product\App\Models\Product; // Import the Product model

class ConfigureMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:configure-meilisearch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configures Meilisearch indexes with specific settings.';

    protected MeilisearchClient $meilisearch;

    public function __construct(MeilisearchClient $meilisearch)
    {
        parent::__construct();
        $this->meilisearch = $meilisearch;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Meilisearch configuration...');

        $productModel = new Product();
        $indexName = $productModel->searchableAs();

        try {
            $index = $this->meilisearch->index($indexName);

            $this->info("Configuring index: {$indexName}");

            // 1. Filterable Attributes
            $filterableAttributes = [
                'id',
                'sku',
                'status',
                'in_stock',
                'original_price',
                'special_price',
                'brand_id',
                'category_ids'
            ];
            $index->updateFilterableAttributes($filterableAttributes);
            $this->line("- Updated filterable attributes: " . implode(', ', $filterableAttributes));

            $sortableAttributes = [
                'product_name',
                'original_price',
                'special_price',
            ];
            $index->updateSortableAttributes($sortableAttributes);
            $this->line("- Updated sortable attributes: " . implode(', ', $sortableAttributes));

            $searchableAttributes = [
                'product_name',
                'categories',
                'attributes',
                'brand_name',
                'sku'
            ];
            $index->updateSearchableAttributes($searchableAttributes);
            $this->line("- Updated searchable attributes: " . implode(', ', $searchableAttributes));

            $rankingRules = [
                'words',
                'typo',
                'proximity',
                'attribute',
                'sort',
                'exactness',
            ];
            $index->updateRankingRules($rankingRules);
            $this->line("- Updated ranking rules: " . implode(', ', $rankingRules));

            // $index->updateSynonyms([...]);
            $index->updateStopWords([
              'a', 'an', 'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with'
            ]);

            $this->info("Configuration for index '{$indexName}' applied successfully.");

        } catch (ApiException $e) {
            $this->error("Meilisearch API Error: " . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error("An unexpected error occurred: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
