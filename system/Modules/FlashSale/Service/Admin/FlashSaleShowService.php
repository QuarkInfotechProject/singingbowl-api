<?php
namespace Modules\FlashSale\Service\Admin;

use Modules\FlashSale\App\Models\FlashSale;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Modules\Color\App\Models\Color;

class FlashSaleShowService
{
    /**
     * Get the flash sale details by its ID.
     *
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getById(int $id)
    {
        try {
            $flashSale = FlashSale::select('id', 'campaign_name', 'start_date', 'end_date', 'theme_color', 'text_color')
                ->with(['files' => function ($query) {
                    $query->whereIn('model_files.zone', ['desktopBanner', 'mobileBanner']);
                }])
                ->find($id);

            if (!$flashSale) {
                throw new Exception('Flash sale not found.', ErrorCode::NOT_FOUND);
            }

            $productUuids = $flashSale->flashSaleProducts()
                ->join('products', 'flash_sale_products.product_id', '=', 'products.id')
                ->pluck('products.uuid')
                ->map(fn($uuid) => (string)$uuid)
                ->toArray();

            $themeColorHex = Color::where('id', $flashSale->theme_color)->value('hex_code');
            $textColorHex = Color::where('id', $flashSale->text_color)->value('hex_code');

            return [
                'id' => $flashSale->id,
                'campaign_name' => $flashSale->campaign_name,
                'start_date' => $flashSale->start_date,
                'end_date' => $flashSale->end_date,
                'theme_color' => [
                    'id' => $flashSale->theme_color,
                    'hex_code' => $themeColorHex,
                ],
                'text_color' => [
                    'id' => $flashSale->text_color,
                    'hex_code' => $textColorHex,
                ],
                'product_id' => $productUuids,
                'files' => $this->getMediaFiles($flashSale),
            ];
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get media files related to the flash sale (desktop and mobile banners).
     *
     * @param FlashSale $flashSale
     * @return array
     */
    private function getMediaFiles(FlashSale $flashSale): array
    {
        $files = $flashSale->files->keyBy(fn($file) => $file->pivot->zone);

        return [
            'desktopBanner' => $files->get('desktopBanner') ? [
                'id' => $files->get('desktopBanner')->id,
                'url' => url($files->get('desktopBanner')->url),
            ] : null,
            'mobileBanner' => $files->get('mobileBanner') ? [
                'id' => $files->get('mobileBanner')->id,
                'url' => url($files->get('mobileBanner')->url),
            ] : null,
        ];
    }
}
