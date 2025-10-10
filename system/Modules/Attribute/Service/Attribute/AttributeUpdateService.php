<?php

namespace Modules\Attribute\Service\Attribute;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Attribute\App\Models\Attribute;
use Modules\Attribute\App\Models\AttributeValue;
use Modules\Attribute\Service\Attribute\DTO\AttributeUpdateDTO;
use Modules\Shared\App\Events\AdminUserActivityLogEvent;
use Modules\Shared\Constant\ActivityTypeConstant;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class AttributeUpdateService
{
    function update(AttributeUpdateDTO $attributeUpdateDTO, string $ipAddress): Attribute
    {
        $attribute = Attribute::find($attributeUpdateDTO->id);

        if (!$attribute) {
            throw new Exception('Attribute not found.', ErrorCode::NOT_FOUND);
        }

        DB::beginTransaction();

        try {
            $attribute->update([
                'attribute_set_id' => $attributeUpdateDTO->attributeSetId,
                'name'             => $attributeUpdateDTO->name,
                'is_enabled'       => $attributeUpdateDTO->is_enabled,
                'sort_order'       => $attributeUpdateDTO->sort_order,
            ]);

            $normalizedValues = [];
            $values = is_array($attributeUpdateDTO->values) ? $attributeUpdateDTO->values : [];

            if (!empty($values)) {
                $existingValues = $attribute->values()->get()->keyBy('value');

                foreach ($values as $value) {
                    if (is_string($value)) {
                        if ($existingValues->has($value)) {
                            $normalizedValues[] = [
                                'id' => $existingValues[$value]->id,
                                'value' => $value
                            ];
                        } else {
                            $normalizedValues[] = [
                                'id' => null,
                                'value' => $value
                            ];
                        }
                    } else if (is_array($value) && isset($value['value'])) {
                        if (empty($value['id']) && $existingValues->has($value['value'])) {
                            $value['id'] = $existingValues[$value['value']]->id;
                        }
                        $normalizedValues[] = $value;
                    }
                }
            }

            $existingValueIds = collect($normalizedValues)->pluck('id')->filter();
            $attribute->values()->whereNotIn('id', $existingValueIds)->delete();

            $newValues = [];
            foreach ($normalizedValues as $value) {
                if (!empty($value['value'])) {
                    $newValues[] = [
                        'id'           => $value['id'] ?? null,
                        'attribute_id' => $attribute->id,
                        'value'        => $value['value'],
                    ];
                }
            }

            if (!empty($newValues)) {
                AttributeValue::upsert($newValues, ['id'], ['attribute_id', 'value']);
            }

            if (isset($attributeUpdateDTO->category_ids)) {
                $categoryIds = is_array($attributeUpdateDTO->category_ids) ? $attributeUpdateDTO->category_ids : [];
                $attribute->categories()->sync($categoryIds);
            }

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();

            Log::error('Failed to update attribute.', [
                'error'          => $exception->getMessage(),
                'attribute_id'   => $attributeUpdateDTO->id,
                'update_data'    => [
                    'attribute_set_id' => $attributeUpdateDTO->attributeSetId,
                    'name'             => $attributeUpdateDTO->name,
                    'is_enabled'       => $attributeUpdateDTO->is_enabled,
                    'sort_order'       => $attributeUpdateDTO->sort_order,
                    'value_count'      => count($attributeUpdateDTO->values ?? []),
                    'category_ids_provided' => isset($attributeUpdateDTO->category_ids),
                ],
                'ip_address'     => $ipAddress
            ]);

            throw $exception;
        }

        Event::dispatch(
            new AdminUserActivityLogEvent(
                'Attribute updated of name: ' . $attribute->name,
                $attribute->id,
                ActivityTypeConstant::ATTRIBUTE_UPDATED,
                $ipAddress)
        );

        return $attribute->refresh();
    }
}
