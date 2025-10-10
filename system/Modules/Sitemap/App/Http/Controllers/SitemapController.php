<?php

namespace Modules\Sitemap\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Product\App\Models\Product;
use Modules\Blog\App\Models\Post;

class SitemapController extends Controller
{
    /**
     * Generate sitemap for products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        $query = Product::query()
            ->where('status', true)
            ->select('slug', 'updated_at')
            ->latest('updated_at');

        if ($request->has('updated_after')) {
            $query->where('updated_at', '>=', $request->input('updated_after'));
        }

        $products = $query->get();

        $sitemap = $products->map(function ($product) {
            return [
                'loc' => url('/products/' . $product->slug),
                'lastmod' => $product->updated_at->toIso8601String(),
            ];
        });

        return response()->json($sitemap);
    }

    /**
     * Generate sitemap for blog posts.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function posts(Request $request)
    {
        $query = Post::query()
            ->where('is_active', true)
            ->select('slug', 'updated_at')
            ->latest('updated_at');

        if ($request->has('updated_after')) {
            $query->where('updated_at', '>=', $request->input('updated_after'));
        }

        $posts = $query->get();

        $sitemap = $posts->map(function ($post) {
            return [
                'loc' => url('/blog/' . $post->slug),
                'lastmod' => $post->updated_at->toIso8601String(),
            ];
        });

        return response()->json($sitemap);
    }
}
