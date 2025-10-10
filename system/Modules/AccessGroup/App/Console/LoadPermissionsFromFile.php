<?php

namespace Modules\AccessGroup\App\Console;

use Illuminate\Console\Command;
use Modules\AccessGroup\DTO\SetPermissionDTO;
use Modules\AccessGroup\Service\Permission\PermissionHandlerService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class LoadPermissionsFromFile extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'load:permissions {--update}';

    /**
     * The console command description.
     */
    protected $description = 'Load default permissions.';

    /**
     * Create a new command instance.
     */
    public function __construct(private PermissionHandlerService $permissionHandlerService)
    {
        parent::__construct();
        $this->permissionHandlerService = $permissionHandlerService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updateOption = $this->option('update');

        $finder = new Finder();
        $finder->files()->name('*.yaml')->in(module_path('AccessGroup', 'Config'));

        if (!$finder->hasResults()) {
            $this->error('permission file not found.');
            return null;
        }

        $yaml = new Parser();
        $permissions = array();

        foreach ($finder as $file) {
            $permission = $yaml->parse(file_get_contents($file));

            if (!$permission) {
                $this->error('permission not found.');
                return null;
            }

            $permissions = array_merge($permissions, $permission);

        }
        foreach ($permissions as $setPermission) {

            $setPermissionDTO = SetPermissionDTO::from($setPermission);

            $this->permissionHandlerService->handlePermission($setPermissionDTO, $updateOption);
        }

        $this->info('Permission have been inserted successfully.');
        return null;
    }
}
