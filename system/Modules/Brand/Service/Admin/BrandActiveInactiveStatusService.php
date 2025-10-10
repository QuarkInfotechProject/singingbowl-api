<?php
namespace Modules\Brand\Service\Admin;

use Modules\Brand\App\Models\Brand;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class BrandActiveInactiveStatusService
{
    public function changeStatus(int $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            throw new Exception('Brand not found.', ErrorCode::NOT_FOUND);
        }

        $brand->status = $brand->status === 1 ? 0 : 1;
        $brand->save();

        return $brand;
    }
}
