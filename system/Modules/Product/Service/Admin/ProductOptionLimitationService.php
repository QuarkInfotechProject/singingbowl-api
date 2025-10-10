<?php

namespace Modules\Product\Service\Admin;

use Modules\SystemConfiguration\App\Models\SystemConfig;

class ProductOptionLimitationService
{
    public static function getProductOptionLimit(): int
    {
        return (int) SystemConfig::where('name', 'product_option_limitation')->value('value');
    }
}
