<?php
//
//namespace Modules\Product\Service\Admin;
//
//use Illuminate\Support\Facades\Log;
//use Modules\Product\App\Models\Product;
//use Modules\Shared\Exception\Exception;
//use Modules\Shared\ImageUpload\Service\TempImageUploadService;
//use Modules\Shared\StatusCode\ErrorCode;
//
//class ProductUpdateSpecificationsService
//{
//    function __construct(private TempImageUploadService $tempImageUploadService)
//    {
//    }
//
//    function update($data)
//    {
//        $product = Product::where('uuid', $data['productId'])->first();
//
//        if (!$product) {
//            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
//        }
//
//        try {
//            $currentSpecifications = $product->specifications;
//
//            $updatedSpecifications = [];
//
//            foreach ($data['specifications'] as $newSpec) {
//                $existingSpec = $this->findExistingSpecification($currentSpecifications, $newSpec);
//
//                if ($existingSpec) {
//                    $updatedSpec = [
//                        'label' => $newSpec['label'],
//                        'file' => $existingSpec['file']
//                    ];
//
//                    if (isset($newSpec['icon'])) {
//                        $updatedSpec['file'] = $this->uploadIcon($newSpec['icon']);
//                    }
//                } else {
//                    $updatedSpec = [
//                        'label' => $newSpec['label'],
//                        'file' => $this->uploadIcon($newSpec['icon'])
//                    ];
//                }
//
//                $updatedSpecifications[] = $updatedSpec;
//            }
//
//            $product->update(['specifications' => $updatedSpecifications]);
//        } catch (\Exception $e) {
//            Log::error('Error updating product specifications: ' . $e->getMessage());
//            throw $e;
//        }
//    }
//
//    private function findExistingSpecification($currentSpecifications, $newSpec)
//    {
//        foreach ($currentSpecifications as $spec) {
//            if ($spec['label'] === $newSpec['label']) {
//                return $spec;
//            }
//        }
//        return null;
//    }
//
//    private function uploadIcon($icon)
//    {
//        return $this->tempImageUploadService->upload($icon, public_path('modules/productSpecificationIcons'));
//    }
//}
