<?php

namespace Modules\Payment\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Payment\Facades\Gateway;
use Modules\Payment\Gateways\Card;
use Modules\Payment\Gateways\COD;
use Modules\Payment\Gateways\Esewa;
use Modules\Payment\Gateways\IMEPay;
use Modules\Payment\Gateways\Khalti;

class PaymentServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Payment';

    protected string $moduleNameLower = 'payment';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCashOnDelivery();
        $this->registerEsewa();
        $this->registerKhalti();
        $this->registerCard();
        $this->registerIMEPay();
    }

    private function registerCashOnDelivery()
    {
        Gateway::register('cod', new COD());
    }

    private function registerEsewa()
    {
        Gateway::register('esewa', new Esewa());
    }

    private function registerKhalti()
    {
        Gateway::register('khalti', new Khalti());
    }

    private function registerCard()
    {
        Gateway::register('card', new Card());
    }

    private function registerIMEPay()
    {
        Gateway::register('IMEPay', new IMEPay());
    }
}
