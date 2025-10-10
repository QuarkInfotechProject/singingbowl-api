<?php
//
//namespace Modules\Product\Service\Admin;
//
//use Modules\Product\App\Models\Product;
//use Modules\Shared\Exception\Exception;
//use Modules\Shared\ImageUpload\Service\TempImageUploadService;
//use Modules\Shared\StatusCode\ErrorCode;
//
//class ProductAddSpecificationsService
//{
//    function __construct(private TempImageUploadService $tempImageUploadService)
//    {
//    }
//
//    function create($data)
//    {
//        $product = Product::where('uuid', $data['productId'])->first();
//
//        if (!$product) {
//            throw new Exception('Product not found.', ErrorCode::NOT_FOUND);
//        }
//
//        $specifications = [];
//
//        try {
//            foreach ($data['specifications'] as $specification) {
//                $specifications[] = [
//                    'label' => $specification['label'],
//                    'file' => $this->uploadIcon($specification['icon']),
//                ];
//            }
//
//            $product->update(['specifications' => $specifications]);
//
//        } catch (\Exception $e) {
//            dd($e);
//            }
//        }
//
//    private function uploadIcon($icon)
//    {
//        return $this->tempImageUploadService->upload($icon, public_path('modules/productSpecificationIcons'));
//    }
//}
