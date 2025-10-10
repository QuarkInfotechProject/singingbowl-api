<?php

namespace Modules\Product\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Product\App\Models\Product;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([
                'message' => 'Search query cannot be empty.',
                'data' => []
            ], 400);
        }

        $products = Product::search($query)->paginate(15);

        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data' => $products
        ]);
    }
}
