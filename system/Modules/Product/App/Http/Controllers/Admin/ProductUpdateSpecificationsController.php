<?php
//
//namespace Modules\Product\App\Http\Controllers\Admin;
//
//use Modules\Product\App\Http\Requests\ProductAddSpecificationsRequest;
//use Modules\Product\Service\Admin\ProductUpdateSpecificationsService;
//use Modules\Shared\App\Http\Controllers\AdminBaseController;
//
//class ProductUpdateSpecificationsController extends AdminBaseController
//{
//    function __construct(private ProductUpdateSpecificationsService $productUpdateSpecificationsService)
//    {
//    }
//
//    function __invoke(ProductAddSpecificationsRequest $request)
//    {
//        $this->productUpdateSpecificationsService->update($request->all());
//
//        return $this->successResponse('Product specifications has been updated successfully.');
//    }
//}
