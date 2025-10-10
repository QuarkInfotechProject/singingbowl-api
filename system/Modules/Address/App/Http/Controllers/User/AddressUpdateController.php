<?php

namespace Modules\Address\App\Http\Controllers\User;

use Modules\Address\App\Http\Requests\AddressUpdateRequest;
use Modules\Address\Service\User\AddressUpdateService;
use Modules\Shared\App\Http\Controllers\UserBaseController;

class AddressUpdateController extends UserBaseController
{
    function __construct(private AddressUpdateService $addressUpdateService)
    {
    }

    function __invoke(AddressUpdateRequest $request)
    {
        $this->addressUpdateService->update($request->all());

        return $this->successResponse('Address has been updated successfully.');
    }
}
