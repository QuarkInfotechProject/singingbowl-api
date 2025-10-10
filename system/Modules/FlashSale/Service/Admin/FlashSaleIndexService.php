<?php
namespace Modules\FlashSale\Service\Admin;

use Modules\FlashSale\App\Models\FlashSale;
use Modules\Color\App\Models\Color;
use Illuminate\Pagination\Paginator;

class FlashSaleIndexService
{
    public function getAll($request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $flashSales = FlashSale::orderBy('created_at', 'desc')->paginate($perPage);

        $colorIds = $flashSales->flatMap(fn($flashSale) => [$flashSale->theme_color, $flashSale->text_color])
                               ->filter()
                               ->unique()
                               ->values();

        $colors = Color::whereIn('id', $colorIds)
                      ->select(['id', 'hex_code'])
                      ->get()
                      ->keyBy('id');

        foreach ($flashSales as $flashSale) {
            $flashSale->theme_color = [
                'id' => $flashSale->theme_color,
                'hex_code' => $colors->get($flashSale->theme_color)->hex_code ?? null
            ];

            $flashSale->text_color = [
                'id' => $flashSale->text_color,
                'hex_code' => $colors->get($flashSale->text_color)->hex_code ?? null
            ];
        }

        return $flashSales;
    }
}