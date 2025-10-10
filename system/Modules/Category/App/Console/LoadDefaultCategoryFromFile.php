<?php

namespace Modules\Category\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LoadDefaultCategoryFromFile extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'load:category';

    /**
     * The console command description.
     */
    protected $description = 'Loads default category from file.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = base_path('Modules/Category/Config/default_category.json');

        $content = json_decode(file_get_contents($path), true);

        $this->insertCategory($content);

        $this->info('Categories have been inserted successfully.');
    }

    function insertCategory($categories, $parentId = 0) {
        foreach ($categories as $category) {

            $categoryItem = [
                'name' => $category['name'],
                'is_searchable' => $category['searchable'],
                'is_active' => $category['status'],
                'is_displayed' => $category['isDisplayed'],
                'slug' => $category['url'],
                'parent_id' => $parentId,
            ];

            $categoryId = DB::table('categories')->insertGetId($categoryItem);

//            if (isset($category['children'])) {
//                $this->insertCategory($category['children'], $categoryId);
//            }
        }
    }
}
