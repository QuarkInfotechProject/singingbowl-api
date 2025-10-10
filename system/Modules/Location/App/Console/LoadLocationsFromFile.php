<?php

namespace Modules\Location\App\Console;

use Illuminate\Console\Command;
use Modules\Location\Service\LocationSetupService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class LoadLocationsFromFile extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'location:setup';

    /**
     * The console command description.
     */
    protected $description = 'Set country, province and cities.';

    private LocationSetupService $locationSetupService;
    /**
     * Create a new command instance.
     */
    public function __construct(LocationSetupService $locationSetupService)
    {
        parent::__construct();
        $this->locationSetupService = $locationSetupService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $finder = new Finder();
        $finder->files()->name('*.yaml')->in(module_path('Location', 'Config'));

        if (!$finder->hasResults()) {
            $this->error('locations file not found.');
            return null;
        }

        $yaml = new Parser();
        $locations = array();

        foreach ($finder as $file) {
            $location = $yaml->parse(file_get_contents($file));

            if (!$location) {
                $this->error('location not found.');
                return null;
            }

            $locations = array_merge($locations, $location);
        }

        $this->locationSetupService->store($locations);

        $this->info("Locations has been setup successfully.");
    }
}
