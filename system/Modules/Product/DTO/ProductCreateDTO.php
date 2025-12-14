<?php

namespace Modules\Product\DTO;

use Modules\Shared\DTO\Constructor;

class ProductCreateDTO extends Constructor
{
    public string $productName;
    public string $url;
    public int|null $brandId;
    public bool $bestSeller= false;

    public bool $hasVariant = false;
    public int|null $originalPrice;
    public int|null $specialPrice;
    public string|null $specialPriceStart;
    public string|null $specialPriceEnd;
    public string|null $sku;
    public string $description;
    public string|null $additionalDescription;
    public bool $status = true;
    public string|null $saleStart;
    public string|null $saleEnd;
    public int|null $quantity;
    public string|int|float|null $weight;
    public bool|null $inStock;

    /**
     * @var ProductCategoriesDTO[]
     */
    public $categories;

    /**
     * @var ProductOptionsDTO[]
     */
    public $options;

    /**
     * @var ProductVariantsDTO[]
     */
    public $variants;

    /**
     * @var ProductAttributesDTO[]
     */
    public $attributes;

    public string|null $newFrom;
    public string|null $newTo;

    /**
     * @var RelatedProductsDTO[]
     */
    public $relatedProducts;

    /**
     * @var UpSellProductsDTO[]
     */
    public $upSells;

    /**
     * @var CrossSellProductsDTO[]
     */
    public $crossSells;


    /**
     * @var ProductCouponsDTO[]
     */
    public $couponId;
    public $featureId;
    public $activeOfferId;

    public $specifications;

    /**
     * @var ProductKeySpecsDTO[]
     */
    public $keySpecs;
}
