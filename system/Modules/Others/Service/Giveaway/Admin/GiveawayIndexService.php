<?php

namespace Modules\Others\Service\Giveaway\Admin;

use Illuminate\Http\Request;
use Modules\Others\App\Models\Giveaway;
use Carbon\Carbon;

class GiveawayIndexService
{
    public function getAll(Request $request)
    {
        $query = Giveaway::query();
        if ($request->has('start_date') && $request->start_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('updated_at', '>=', $startDate);
        }
        if ($request->has('end_date') && $request->end_date) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('updated_at', '<=', $endDate);
        }

        return $query->orderBy('updated_at', 'desc')
            ->select([
                'fullname',
                'phone_number',
                'email',
                'created_at',
                'updated_at'
            ])
            ->get()
            ->map(function ($giveaway) {
                return [
                    'name' => $giveaway->fullname,
                    'phoneNumber' => $giveaway->phone_number,
                    'email' => $giveaway->email,
                    'created_at' => $giveaway->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $giveaway->updated_at->format('Y-m-d H:i:s'),
                ];
            });
    }
}