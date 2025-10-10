<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $config = array(
            'driver' => $this->getMailSettings('email_transport'),
            'host' => $this->getMailSettings('email_host'),
            'port' => $this->getMailSettings('email_port'),
            'from' => [
                'address' => $this->getMailSettings('email_address'),
                'name' => $this->getMailSettings('email_name')
            ],
            'encryption' => $this->getMailSettings('email_encryption'),
            'username' => $this->getMailSettings('email_username'),
            'password' => $this->getMailSettings('email_password'),
            'name' => $this->getMailSettings('email_name'),
            'sendmail' => '/usr/sbin/sendmail -bs',
            'pretend' => false,
        );
        Config::set('mail', $config);
    }

    public function getMailSettings($key)
    {
        return null;
       // return DB::table('system_config_settings')
          //  ->where('name', '=', $key)
          //  ->pluck('value')->first();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
