<?php

namespace Modules\Address\App\Http\Controllers\User;

use Illuminate\Http\Request;
use Modules\Address\Service\User\AddressShowService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class AddressShowController extends UserBaseController
{
    function __construct(private AddressShowService $addressShowService)
    {
    }

    function __invoke(string $uuid)
    {
        $address = $this->addressShowService->show($uuid);

        return $this->successResponse('Address has been fetched successfully.', $address);
    }
}
