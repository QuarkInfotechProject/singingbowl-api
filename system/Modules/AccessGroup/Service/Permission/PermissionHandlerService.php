<?php

namespace Modules\AccessGroup\Service\Permission;

use Illuminate\Support\Facades\DB;
use Modules\AccessGroup\DTO\SetPermissionDTO;
use Spatie\Permission\Models\Permission;

class PermissionHandlerService
{
    function handlePermission(SetPermissionDTO $setPermissionDTO, $updateOption)
    {
        try {
            DB::beginTransaction();
            if ($updateOption) {
                $this->updatePermission($setPermissionDTO);
            }

            $this->createPermission($setPermissionDTO);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            throw $exception;
        }
    }

    private function createPermission($setPermissionDTO)
    {
        $existingPermission = Permission::where('name', $setPermissionDTO->name)->first();

        if (!$existingPermission) {
            Permission::create([
                'name' => $setPermissionDTO->name,
                'description' => $setPermissionDTO->description,
                'section' => $setPermissionDTO->section,
                'isIndex' => $setPermissionDTO->isIndex,
                'guard_name' => 'admin'
            ]);
        }
    }

    private function updatePermission($setPermissionDTO)
    {
        Permission::findByName($setPermissionDTO->name)->update([
            'name' => $setPermissionDTO->name,
            'description' => $setPermissionDTO->description,
            'section' => $setPermissionDTO->section,
            'isIndex' => $setPermissionDTO->isIndex,
        ]);
    }
}
