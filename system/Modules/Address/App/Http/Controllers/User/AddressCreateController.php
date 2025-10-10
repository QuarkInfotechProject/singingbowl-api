<?php

namespace Modules\Address\App\Http\Controllers\User;

use Modules\Address\App\Http\Requests\AddressCreateRequest;
use Modules\Address\Service\User\AddressCreateService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class AddressCreateController extends UserBaseController
{
    function __construct(private AddressCreateService $addressCreateService)
    {
    }

    function __invoke(AddressCreateRequest $addressCreateRequest)
    {
        $this->addressCreateService->create($addressCreateRequest->all());

        return $this->successResponse('Address has been saved successfully.');
    }
}
