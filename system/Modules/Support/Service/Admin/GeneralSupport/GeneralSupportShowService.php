<?php

namespace Modules\Support\Service\Admin\GeneralSupport;

use Carbon\Carbon;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Support\App\Models\GeneralSupport;

class GeneralSupportShowService
{
    function show(int $id)
    {
        $generalSupport = GeneralSupport::select(
            'name',
            'email',
            'phone',
            'message',
            'created_at'
        )->find($id);

        if (!$generalSupport) {
            throw new Exception('General support not found.', ErrorCode::NOT_FOUND);
        }

        return [
            'name' => $generalSupport->name,
            'email' => $generalSupport->email,
            'phone' => $generalSupport->phone,
            'message' => $generalSupport->message,
            'submittedAt' => Carbon::parse($generalSupport->created_at)->isoFormat('Do MMMM, YYYY @ h:mm A'),
        ];
    }
}
