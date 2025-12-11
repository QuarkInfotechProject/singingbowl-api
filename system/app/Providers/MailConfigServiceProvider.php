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
            'port' => $this->getMailSettings('email_port', 'int'),
            'from' => [
                'address' => $this->getMailSettings('email_address'),
                'name' => $this->getMailSettings('email_name')
            ],
            'encryption' => $this->getMailSettings('email_encryption'),
            'username' => $this->getMailSettings('email_username'),
            'password' => $this->getMailSettings('email_password'),
            'timeout' => $this->getMailSettings('email_timeout', 'int'),
            'name' => $this->getMailSettings('email_name'),
            'sendmail' => '/usr/sbin/sendmail -bs',
            'pretend' => false,
        );
        
        Config::set('mail', $config);
    }

    /**
     * Get mail settings from database with optional type casting
     * 
     * @param string $key The setting name
     * @param string|null $cast Optional type casting ('int', 'bool', 'float')
     * @return mixed
     */
    public function getMailSettings($key, $cast = null)
    {
        try {
            $value = DB::table('system_config_settings')
                ->where('name', '=', $key)
                ->value('value');
            
            // Apply type casting if specified
            if ($cast === 'int' && $value !== null) {
                return (int) $value;
            } elseif ($cast === 'bool' && $value !== null) {
                return (bool) $value;
            } elseif ($cast === 'float' && $value !== null) {
                return (float) $value;
            }
            
            return $value;
        } catch (\Exception $e) {
            \Log::error("Failed to get mail setting '{$key}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
