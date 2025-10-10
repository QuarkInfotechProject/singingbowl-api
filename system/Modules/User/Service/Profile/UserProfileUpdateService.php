<?php

namespace Modules\User\Service\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Shared\ImageUpload\Service\TempImageUploadService;
use Modules\User\App\Models\User;

class UserProfileUpdateService
{
    function __construct(private TempImageUploadService $tempImageUploadService)
    {
    }

    function update($data)
    {
        $user = Auth::user();
        $image = $user->profile_picture;

        if (isset($data['profilePicture'])) {
            $uploadedImage = $this->updateProfilePicture($user, $data['profilePicture']);
            if ($uploadedImage) {
                $image = url('EndUserProfilePicture/' . $uploadedImage);
            }
        }

        try {
            DB::beginTransaction();

            $user->update([
                'phone_no' => $data['phoneNumber'] ?? $user->phone_no,
                'profile_picture' => $image,
                'offers_notification' => $data['offersNotification'] ?? $user->offers_notification,
                'gender' => $data['gender'] ?? $user->gender,
                'date_of_birth' => $data['dateOfBirth'] ?? $user->date_of_birth,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function updateProfilePicture(User $user, $imageData)
    {
        $oldProfilePicture = $user->profile_picture;
        $newProfilePicture = $this->tempImageUploadService->upload($imageData, public_path('/EndUserProfilePicture'));

        if ($newProfilePicture && $oldProfilePicture) {
            $oldFilePath = public_path(parse_url($oldProfilePicture, PHP_URL_PATH));
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }

        return $newProfilePicture;
    }
}
