<?php

namespace Modules\Shared\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\App\Events\OrderLogEvent;
use Modules\Order\App\Events\SendOrderInvoiceMail;
use Modules\Order\App\Events\SendOrderNoteMail;
use Modules\Order\App\Events\SendOrderStatusChangeMail;
use Modules\Order\App\Listeners\OrderLogListener;
use Modules\Order\App\Listeners\SendOrderConfirmationSms;
use Modules\Order\App\Listeners\SendOrderInvoiceMailFired;
use Modules\Order\App\Listeners\SendOrderNoteMailFired;
use Modules\Order\App\Listeners\SendOrderStatusChangeMailFired;
use Modules\OrderProcessing\App\Events\OrderShipped;
use Modules\OrderProcessing\App\Listeners\NotifyUsersOfOrderShipped;
use Modules\Others\App\Events\CategoriesTrendingCreated;
use Modules\Others\App\Events\CategoriesTrendingDeleted;
use Modules\Others\App\Events\CategoriesTrendingUpdated;
use Modules\Others\App\Listeners\CategoriesTrendingCacheInvalidationListener;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\App\Listeners\AdminUserActivityLogListener;
use Modules\User\App\Events\SendActivateMail;
use Modules\User\App\Events\SendBlockMail;
use Modules\User\App\Events\SendPasswordResetLinkMail;
use Modules\User\App\Events\SendRegisterMail;
use Modules\User\App\Events\SendRegistrationSuccessMail;
use Modules\User\App\Events\UserRegistered;
use Modules\User\App\Listeners\SendActivateMailFired;
use Modules\User\App\Listeners\SendBlockMailFired;
use Modules\User\App\Listeners\SendPasswordResetLinkMailFired;
use Modules\User\App\Listeners\SendRegisterMailFired;
use Modules\User\App\Listeners\SendRegistrationSuccessMailFired;
use Modules\User\App\Listeners\SendWelcomeSms;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AdminUserActivityLogEvent::class => [
            AdminUserActivityLogListener::class
        ],

        SendRegisterMail::class => [
            SendRegisterMailFired::class
        ],

        SendBlockMail::class => [
            SendBlockMailFired::class
        ],

        SendActivateMail::class => [
            SendActivateMailFired::class
        ],

        SendRegistrationSuccessMail::class => [
            SendRegistrationSuccessMailFired::class
        ],

        // UserRegistered::class => [
        //     SendWelcomeSms::class
        // ],

        SendPasswordResetLinkMail::class => [
            SendPasswordResetLinkMailFired::class
        ],

        SendOrderInvoiceMail::class => [
            SendOrderInvoiceMailFired::class,
            // SendOrderConfirmationSms::class
        ],

        OrderShipped::class => [
            NotifyUsersOfOrderShipped::class
        ],

        OrderLogEvent::class => [
            OrderLogListener::class
        ],

        SendOrderNoteMail::class => [
            SendOrderNoteMailFired::class,
        ],

        SendOrderStatusChangeMail::class => [
            SendOrderStatusChangeMailFired::class,
        ],

        // CategoriesTrending Events
        CategoriesTrendingCreated::class => [
            CategoriesTrendingCacheInvalidationListener::class
        ],

        CategoriesTrendingUpdated::class => [
            CategoriesTrendingCacheInvalidationListener::class
        ],

        CategoriesTrendingDeleted::class => [
            CategoriesTrendingCacheInvalidationListener::class
        ]
    ];
}
