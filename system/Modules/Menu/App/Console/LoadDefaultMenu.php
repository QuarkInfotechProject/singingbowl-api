<?php

namespace Modules\Menu\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LoadDefaultMenu extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'load:menu';

    /**
     * The console command description.
     */
    protected $description = 'Load default menu.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = base_path('Modules/Menu/Config/default_menu.json');

        $content = json_decode(file_get_contents($path), true);

        $this->insertMenuItems($content);

        $this->info('Menu have been inserted successfully.');
    }

    function insertMenuItems($items, $parentId = 0) {
        foreach ($items as $item) {
            $existingItem = DB::table('menus')
                ->where('title', $item['title'])
                ->where('parent_id', $parentId)
                ->first();
            if ($existingItem) {
                DB::table('menus')
                    ->where('id', $existingItem->id)
                    ->update([
                        'url' => $item['url'],
                        'icon' => $item['icon'],
                        'permission_name' => $item['permission_name'],
                        'sort_order' => $item['sort_order'],
                        'status' => $item['status']
                    ]);
                $itemId = $existingItem->id;
            } else {
                $menuItem = [
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'icon' => $item['icon'],
                    'permission_name' => $item['permission_name'],
                    'sort_order' => $item['sort_order'],
                    'status' => $item['status'],
                    'parent_id' => $parentId,
                ];
                $itemId = DB::table('menus')->insertGetId($menuItem);
            }
            if (isset($item['children'])) {
                $this->insertMenuItems($item['children'], $itemId);
            }
        }
    }
}
