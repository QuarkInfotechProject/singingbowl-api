<?php

namespace Modules\Address\App\Http\Controllers\User;

use Modules\Address\Service\User\AddressIndexService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class AddressIndexController extends UserBaseController
{
    function __construct(private AddressIndexService $addressIndexService)
    {
    }

    function __invoke()
    {
        $address = $this->addressIndexService->index();

        return $this->successResponse('Address has been fetched successfully.', $address);
    }
}
