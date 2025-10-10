<?php

namespace Modules\Attribute\Service\Attribute;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\App\Models\Attribute;
use Modules\Attribute\Service\Attribute\DTO\AttributeCreateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;

class AttributeCreateService
{
    function create(AttributeCreateDTO $attributeCreateDTO, string $ipAddress): Attribute
    {
        $now = Carbon::now();

        DB::beginTransaction();

        try {
            $attribute = Attribute::create([
                'attribute_set_id' => $attributeCreateDTO->attributeSetId,
                'name'             => $attributeCreateDTO->name,
                'is_enabled'       => $attributeCreateDTO->is_enabled ?? true,
                'sort_order'       => $attributeCreateDTO->sort_order,
            ]);

            $attributeValues = [];
            $values = is_array($attributeCreateDTO->values) ? $attributeCreateDTO->values : [];

            foreach ($values as $value) {
                if (is_string($value) && !empty($value)) {
                    $attributeValues[] = [
                        'attribute_id' => $attribute->id,
                        'value'        => $value,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                } else if (is_array($value) && isset($value['value']) && !empty($value['value'])) {
                    $attributeValues[] = [
                        'attribute_id' => $attribute->id,
                        'value'        => $value['value'],
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }
            }

            if (!empty($attributeValues)) {
                DB::table('attribute_values')->insert($attributeValues);
            }

            if (isset($attributeCreateDTO->category_ids)) {
                $categoryIds = is_array($attributeCreateDTO->category_ids) ? $attributeCreateDTO->category_ids : [];
                $attribute->categories()->sync($categoryIds);
            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();

            Log::error('Failed to create attribute.', [
                'error'          => $exception->getMessage(),
                'attribute_set_id' => $attributeCreateDTO->attributeSetId,
                'name'           => $attributeCreateDTO->name,
                'is_enabled'     => $attributeCreateDTO->is_enabled ?? true,
                'sort_order'     => $attributeCreateDTO->sort_order,
                'value_count'    => count($attributeCreateDTO->values ?? []),
                'category_ids_provided' => isset($attributeCreateDTO->category_ids),
                'ip_address'     => $ipAddress
            ]);

            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Attribute created of name: ' . $attribute->name,
                $attribute->id,
                ActivityTypeConstant::ATTRIBUTE_CREATED,
                $ipAddress)
        );

        return $attribute->refresh();
    }
}
