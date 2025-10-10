<?php

namespace Modules\Color\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Color\App\Models\Color;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LoadDefaultColor extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'load:colors';

    /**
     * The console command description.
     */
    protected $description = 'Load default colors.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = base_path('Modules/Color/Config/default_color.json');

        $colors = json_decode(file_get_contents($path), true);

        $colorData = [];

        foreach ($colors as $color) {
            $colorData[] = [
                'name' => $color['name'],
                'hex_code' => $color['hexCode'],
                'status' => $color['status'],
            ];
        }

        Color::insert($colorData);

        $this->info('Colors have been inserted successfully.');
    }
}
