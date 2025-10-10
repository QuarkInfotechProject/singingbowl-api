<?php

namespace App\Helpers;

use Picqer\Barcode\BarcodeGeneratorHTML;

class BarcodeHelper
{
    protected static $generator;

    protected static function getGenerator()
    {
        if (!self::$generator) {
            self::$generator = new BarcodeGeneratorHTML();
        }
        return self::$generator;
    }

    public static function getBarcodeHTML($code, $type = 'C128', $widthFactor = 2, $height = 50)
    {
        $generator = self::getGenerator();

        // Map barcode types from milon/barcode to picqer format
        $typeMap = [
            'C128' => $generator::TYPE_CODE_128,
            'C39' => $generator::TYPE_CODE_39,
            'EAN13' => $generator::TYPE_EAN_13,
            'EAN8' => $generator::TYPE_EAN_8,
            'UPC' => $generator::TYPE_UPC_A,
        ];

        $barcodeType = $typeMap[$type] ?? $generator::TYPE_CODE_128;

        try {
            return $generator->getBarcode($code, $barcodeType, $widthFactor, $height);
        } catch (\Exception $e) {
            return '<div style="color: red;">Barcode generation error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
