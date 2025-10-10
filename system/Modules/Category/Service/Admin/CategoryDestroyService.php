<?php

namespace Modules\Category\Service\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Category\App\Models\Category;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class CategoryDestroyService
{
    function destroy(int $id, string $ipAddress)
    {
        $categoryToDelete = Category::find($id);

        if (!$categoryToDelete) {
            throw new Exception('Category not found', ErrorCode::NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            $this->deleteFilesForCategory($categoryToDelete);

            $this->deleteCategoryItemAndChildren($categoryToDelete, $ipAddress);

            DB::commit();
        } catch (\Exception $exception) {
            Log::error('Failed to destroy category.', [
                'error' => $exception->getMessage(),
                'category_id' => $id,
                'ip_address' => $ipAddress
            ]);
            DB::rollBack();
            throw $exception;
        }
    }

    protected function deleteCategoryItemAndChildren($item, $ipAddress)
    {
        try {
            $this->deleteFilesForCategory($item);

            $item->delete();

            $children = Category::where('parent_id', $item->id)->get();
            foreach ($children as $child) {
                $this->deleteCategoryItemAndChildren($child, $ipAddress);
            }

            Event::dispatch(
                new AdminUserActivityLogEvent(
                    'Category destroyed of name: ' . $item->name,
                    $item->id,
                    ActivityTypeConstant::CATEGORY_DESTROYED,
                    $ipAddress
                )
            );
        } catch (\Exception $exception) {
            Log::error('Failed to delete category item and children.', [
                'error' => $exception->getMessage(),
                'category_id' => $item->id,
                'ip_address' => $ipAddress
            ]);
            throw $exception;
        }
    }

    private function deleteFilesForCategory($categoryToDelete)
    {
        try {
            DB::table('model_files')
                ->where('model_type', 'Modules\Category\App\Models\Category')
                ->where('model_id', $categoryToDelete->id)
                ->delete();
        } catch (\Exception $exception) {
            Log::error('Failed to delete files for category.', [
                'error' => $exception->getMessage(),
                'category_id' => $categoryToDelete->id
            ]);
            throw $exception;
        }
    }
}
