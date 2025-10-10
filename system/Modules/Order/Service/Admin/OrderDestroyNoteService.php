<?php

namespace Modules\Order\Service\Admin;

use Illuminate\Support\Facades\DB;
use Modules\Order\App\Models\OrderLog;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class OrderDestroyNoteService
{
    function destroy($data)
    {
        try {
            DB::beginTransaction();

            $note = OrderLog::where('id', $data['noteId'])
                ->where('order_id', $data['orderId'])
                ->first();

            if (!$note) {
                throw new Exception('Note not found.', ErrorCode::NOT_FOUND);
            }

            $note->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
