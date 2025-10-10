<?php

namespace Modules\SystemConfiguration\App\Console;

use Illuminate\Console\Command;
use Modules\SystemConfiguration\DTO\EmailTemplateDTO;
use Modules\SystemConfiguration\Service\EmailTemplate\EmailTemplateCreateService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class LoadEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:template {--update}';

    /**
     * The console command description.
     */
    protected $description = 'Load email templates from file.';

    /**
     * Create a new command instance.
     */
    public function __construct(private EmailTemplateCreateService $emailTemplateCreateService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updateOption = $this->option('update');

        $finder = new Finder();
        $finder->files()->name('email_template.yaml')->in(module_path('SystemConfiguration', 'Config/'));

        if (!$finder->hasResults()) {
            $this->error('Email configurations file not found.');
            return null;
        }

        $yaml = new Parser();
        $emailConfigurations = array();

        foreach ($finder as $file) {
            $configurations = $yaml->parse(file_get_contents($file));

            if (!$configurations) {
                $this->error('Email configurations not found.');
                return null;
            }
            $emailConfigurations = array_merge($emailConfigurations, $configurations);
        }

        foreach ($emailConfigurations as $emailConfiguration) {
            $emailConfigurationDTO = EmailTemplateDTO::from($emailConfiguration);

            $this->emailTemplateCreateService->create($emailConfigurationDTO, $updateOption);
        }

        $this->info('Email configurations have been set successfully.');
        return null;

    }
}
