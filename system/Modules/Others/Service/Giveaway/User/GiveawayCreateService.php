<?php

namespace Modules\Others\Service\Giveaway\User;

use Modules\Others\App\Models\Giveaway;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class GiveawayCreateService
{
    public function create(array $data): Giveaway
    {
        $validated = $this->validate($data);

        $giveaway = Giveaway::where('phone_number', $validated['phone_number'])->first();

        if ($giveaway) {
            $giveaway->update([
                'fullname' => $validated['fullname'],
                'email' => $validated['email'],
            ]);
            $giveaway->touch();
            return $giveaway;
        }
        return Giveaway::create([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
        ]);
    }

    private function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone_number' => [
                'required',
                'digits:10',
                'regex:/^9[0-9]{9}$/',
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}