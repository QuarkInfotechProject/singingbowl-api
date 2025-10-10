<?php

namespace Modules\Product\DTO;

use Modules\Shared\DTO\Constructor;

class ProductUpdateDTO extends Constructor
{
    public string $uuid;
    public string $productName;
    public string $url;
    public int|null $brandId;
    public int $sortOrder;

    public bool $hasVariant;
    public int|null $originalPrice;
    public int|null $specialPrice;
    public string|null $specialPriceStart;
    public string|null $specialPriceEnd;
    public string|null $sku;
    public string $description;
    public string|null $additionalDescription;
    public bool $status;
    public string|null $saleStart;
    public string|null $saleEnd;
    public int|null $quantity;
    public bool|null $inStock;
    public string|null $newFrom;
    public string|null $newTo;

    /**
     * @var ProductCategoriesDTO[]
     */
    public $categories;

    /**
     * @var ProductTagsDTO[]
     */
    public $tags;

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
    public $keySpecs;
    public bool $bestSeller = false;

}
