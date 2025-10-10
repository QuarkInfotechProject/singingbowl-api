<?php

namespace Modules\SystemConfiguration\App\Console;

use Illuminate\Console\Command;
use Modules\SystemConfiguration\Service\Setting\SettingCreateService;
use Src\Config\Service\AddSystemConfigService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class LoadSystemConfigSettings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'load:system-config';

    /**
     * The console command description.
     */
    protected $description = 'Load system configuration settings.';

    /**
     * Create a new command instance.
     */
    public function __construct(private SettingCreateService $settingCreateService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $finder = new Finder();
        $finder->files()->name('system_config.yaml')->in(module_path('SystemConfiguration', 'Config/'));
        if (!$finder->hasResults()) {
            $this->error('config file not found.');
            return null;
        }

        $yaml = new Parser();
        $configs = array();
        foreach ($finder as $file) {
            $config = $yaml->parse(file_get_contents($file));

            if (!$config) {
                $this->error('config not found.');
                return null;
            }

            $configs = array_merge($configs, $config);

        }

        foreach ($configs as $config) {
            $this->settingCreateService->create($config);
        }


        $this->info('Configurations have been inserted successfully.');
        return null;

    }
}
