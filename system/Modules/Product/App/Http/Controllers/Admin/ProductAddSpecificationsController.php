<?php
//
//namespace Modules\Product\App\Http\Controllers\Admin;
//
//use Modules\Product\App\Http\Requests\ProductAddSpecificationsRequest;
//use Modules\Product\Service\Admin\ProductAddSpecificationsService;
//use Modules\Shared\App\Http\Controllers\AdminBaseController;
//
//class ProductAddSpecificationsController extends AdminBaseController
//{
//    function __construct(private ProductAddSpecificationsService $productAddSpecificationsService)
//    {
//    }
//
//    function __invoke(ProductAddSpecificationsRequest $request)
//    {
//        $this->productAddSpecificationsService->create($request->all());
//
//        return $this->successResponse('Specifications has been added successfully.');
//    }
//}
