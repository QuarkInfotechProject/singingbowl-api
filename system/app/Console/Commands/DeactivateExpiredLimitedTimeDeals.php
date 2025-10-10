<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Others\Service\LimitedTimeDeals\Admin\LimitedTimeDealExpirationService;

class DeactivateExpiredLimitedTimeDeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deals:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate limited time deals that have expired based on their product special price end date';

    /**
     * The expiration service instance.
     *
     * @var LimitedTimeDealExpirationService
     */
    protected $expirationService;

    /**
     * Create a new command instance.
     */
    public function __construct(LimitedTimeDealExpirationService $expirationService)
    {
        parent::__construct();
        $this->expirationService = $expirationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to deactivate expired limited time deals...');

        try {
            $result = $this->expirationService->deactivateExpiredDeals();
            
            if ($result['count'] > 0) {
                $this->info("Successfully deactivated {$result['count']} expired limited time deals.");
                
                // Display details of deactivated deals
                if (!empty($result['deactivated_deals'])) {
                    $this->table(
                        ['Deal ID', 'Product Name', 'Expired Date'],
                        collect($result['deactivated_deals'])->map(function ($deal) {
                            return [
                                $deal['id'],
                                $deal['product_name'],
                                $deal['expired_date']
                            ];
                        })->toArray()
                    );
                }
            } else {
                $this->info('No expired limited time deals found.');
            }

        } catch (\Exception $e) {
            $this->error('Error occurred while deactivating expired deals: ' . $e->getMessage());
            return 1;
        }

        $this->info('Command completed successfully.');
        return 0;
    }
}
