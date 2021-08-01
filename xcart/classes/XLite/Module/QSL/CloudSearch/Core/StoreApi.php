<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite;
use XLite\Core\CommonCell;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Translation;
use XLite\Model\Category;
use XLite\Model\CategoryProducts;
use XLite\Model\Product;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\Module\QSL\CloudSearch\Model\Repo\Category as CategoryRepo;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;
use XLite\Module\QSL\CloudSearch\Model\Repo\Page as PageRepo;


/**
 * CloudSearch store-side API methods
 */
class StoreApi extends \XLite\Base\Singleton
{
    /*
     * Maximum number of entities to include in API call response
     */
    const MAX_ENTITIES_AT_ONCE = 300;
    const MAX_VARIANTS_AT_ONCE = 6000;

    /*
     * Maximum thumbnail width/height
     */
    const MAX_THUMBNAIL_WIDTH = 300;
    const MAX_THUMBNAIL_HEIGHT = 300;

    protected $categoryCache = [];

    protected $priceCache = [];

    protected $activeLanguages = null;

    /**
     * Get API summary - entity counts and supported features
     *
     * @return array
     */
    public function getApiSummary()
    {
        /** @var ProductRepo $repo */
        $repo        = Database::getRepo('XLite\Model\Product');
        $numProducts = $repo->search($this->getProductSearchConditions(), $repo::SEARCH_MODE_COUNT);

        $catRepo       = Database::getRepo('XLite\Model\Category');
        $numCategories = $catRepo->search($this->getCategorySearchConditions(), $catRepo::SEARCH_MODE_COUNT);

        $pageRepo = Database::getRepo('\XLite\Module\CDev\SimpleCMS\Model\Page');
        $numPages = $pageRepo ? $pageRepo->search($this->getPageSearchConditions(), $pageRepo::SEARCH_MODE_COUNT) : 0;

        return [
            'numProducts'        => $numProducts,
            'numCategories'      => $numCategories,
            'numManufacturers'   => $this->getBrandsCount(),
            'numPages'           => $numPages,
            'productsAtOnce'     => $this->getMaxEntitiesAtOnce(),
            'features'           => [
                'cloud_filters', 'real_time_indexing', 'multi_lingual', 'admin_search',
                'customizable_category_price', 'widget_settings', 'custom_category_filter',
            ],
            'availableLanguages' => $this->getActiveLanguages(),
            'defaultLanguage'    => $this->getDefaultCustomerLanguage(),
        ];
    }

    protected function getActiveLanguages()
    {
        if (null === $this->activeLanguages) {
            $result = [];

            foreach (Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
                $result[] = $language->getCode();
            }

            $this->activeLanguages = $result;
        }

        return $this->activeLanguages;
    }

    protected function getDefaultCustomerLanguage()
    {
        return Config::getInstance()->General->default_language;
    }

    protected function getFallbackLanguages()
    {
        return [$this->getDefaultCustomerLanguage(), Translation::DEFAULT_LANGUAGE];
    }

    protected function getInactiveFallbackLanguages()
    {
        return array_diff($this->getFallbackLanguages(), $this->getActiveLanguages());
    }

    /**
     * Get product search conditions when indexing the catalog
     *
     * @param $params
     *
     * @return CommonCell
     */
    protected function getProductSearchConditions($params = [])
    {
        $cnd = new CommonCell();

        if (Config::getInstance()->General->show_out_of_stock_products !== 'directLink'
            || Main::isAdminSearchEnabled()
        ) {
            $cnd->{ProductRepo::P_INVENTORY} = ProductRepo::INV_ALL;
        } else {
            $cnd->{ProductRepo::P_INVENTORY} = ProductRepo::INV_IN;
        }

        $cnd->{ProductRepo::P_SKIP_MEMBERSHIP_CONDITION} = true;

        if (isset($params['start']) && isset($params['limit'])) {
            $cnd->{ProductRepo::P_LIMIT} = [$params['start'], $params['limit']];
        }

        if (isset($params['ids'])) {
            $cnd->{ProductRepo::P_CLOUD_SEARCH_PRODUCT_IDS} = $params['ids'];
        }

        return $cnd;
    }

    /**
     * Get category search conditions when indexing the catalog
     *
     * @param $params
     *
     * @return CommonCell
     */
    protected function getCategorySearchConditions($params = [])
    {
        $cnd = new CommonCell();

        if (isset($params['start']) && isset($params['limit'])) {
            $cnd->{CategoryRepo::P_LIMIT} = [$params['start'], $params['limit']];
        }

        if (isset($params['ids'])) {
            $cnd->{CategoryRepo::P_CLOUD_SEARCH_CATEGORY_IDS} = $params['ids'];
        }

        return $cnd;
    }

    /**
     * Get page search conditions when indexing the catalog
     *
     * @return CommonCell
     */
    protected function getPageSearchConditions()
    {
        return new CommonCell([PageRepo::PARAM_ENABLED => true]);
    }

    /**
     *
     * Maximum number of entities returned in one API response
     *
     * @return int
     */
    protected function getMaxEntitiesAtOnce()
    {
        return static::MAX_ENTITIES_AT_ONCE;
    }

    /**
     * @return int
     */
    protected function getBrandsCount()
    {
        return 0;
    }

    /**
     * Get products data
     *
     * @return array
     */
    public function getBrands()
    {
        return [];
    }

    /**
     * Get products data
     *
     * @param $params
     *
     * @return array
     */
    public function getProducts($params)
    {
        $cnd = $this->getProductSearchConditions($params);

        /** @var ProductRepo $repo */
        $repo = Database::getRepo('XLite\Model\Product');

        $products = [];

        $variantsCount = 0;

        foreach ($repo->search($cnd, ProductRepo::SEARCH_MODE_ENTITIES) as $p) {
            $product = $this->getProduct($p);

            $variantsCount += count($product['variants']);

            $products[] = $product;

            if ($variantsCount > self::MAX_VARIANTS_AT_ONCE) {
                break;
            }
        }

        return $products;
    }

    /**
     * Get single product data
     *
     * @param Product $product
     *
     * @return array
     */
    public function getProduct(Product $product)
    {
        $skus = implode(' ', $this->getSkus($product));

        $url = $this->getItemUrl('product', ['product_id' => $product->getProductId()]);

        $data = [
                'id'         => $product->getProductId(),
                'sku'        => $skus,
                'price'      => $this->getProductPrice($product),
                'url'        => $url,
                'membership' => $product->getMembershipIds(),
            ]
            + $this->getProductImage($product)
            + $this->getProductCategoryData($product)
            + $this->getProductTranslations($product);

        $data['modifiers'] = [];

        $filterableAttributes = $this->getFilterableProductAttributes($product);

        $data['modifiers']   = $this->getSearchableProductAttributes($product);
        $data['modifiers'][] = $this->getProductMetaInfo($product);

        $data['variants'] = $this->getProductVariants($product, $filterableAttributes);

        $data['conditions'] = $this->getProductConditions($product);

        $data += $this->getSortFields($product);

        $this->formatProductMultiLingualFields($data);

        return $data;
    }

    protected function getProductTranslations(Product $product)
    {
        $data = [];

        $productTranslations = [];
        foreach ($product->getTranslations() as $t) {
            if (isset($productTranslations[$t->getCode()])) {
                continue;
            }

            $productTranslations[$t->getCode()] = [
                'name'        => $t->getName(),
                'description' => $t->getDescription() ?: $t->getBriefDescription(),
            ];
        }

        foreach ($this->getActiveLanguages() as $lang) {
            $data["name_$lang"]        = $this->getFieldTranslation($productTranslations, $lang, 'name');
            $data["description_$lang"] = $this->getFieldTranslation($productTranslations, $lang, 'description');
        }

        return $data;
    }

    protected function getFieldTranslation($translations, $lang, $field = null)
    {
        $langs = array_merge([$lang], $this->getFallbackLanguages());

        $translation = null;

        foreach ($langs as $l) {
            if (isset($translations[$l])) {
                $translation = $translations[$l];
                break;
            }
        }

        $result = $field ? (isset($translation[$field]) ? $translation[$field] : null) : $translation;

        if (!$result) {
            if (!empty($translations)) {
                $t = array_shift($translations);

                return $field ? (isset($t[$field]) ? $t[$field] : '') : $t;
            }
        } else {
            return $result;
        }

        return '';
    }

    /**
     * Get sort fields that can be used to sort CloudSearch search results.
     * Sort fields are dynamic in the way that custom sort_int_*, sort_float_*, sort_str_* are allowed.
     *
     * @param Product $product
     *
     * @return array
     */
    protected function getSortFields(Product $product)
    {
        $fields = [
            'sort_int_arrival_date' => $product->getArrivalDate(),
            'sort_float_price'      => $this->getProductPrice($product),
            'sort_int_sales'        => $product->getSales(),
            'sort_str_sku'          => $product->getSku(),
            'sort_int_amount'       => $product->getInventoryEnabled()
                ? $product->getPublicAmount()
                : $product->getMaxPurchaseLimit(),
        ];

        /** @var CategoryProducts $cp */
        foreach ($product->getCategoryProducts() as $cp) {
            $fields['sort_int_orderby_category_' . $cp->getCategory()->getId()] = $cp->getOrderby();
        }

        return $fields;
    }

    /**
     * Get product price.
     *
     * @param Product $product
     *
     * @return float
     */
    protected function getProductPrice(Product $product)
    {
        $id = $product->getProductId();

        if (!isset($this->priceCache[$id])) {
            $quickData = $product->getQuickData();

            if ($quickData->isEmpty()) {
                $this->priceCache[$id] = $product->getDisplayPrice();
            } else {
                foreach ($quickData as $qd) {
                    if ($qd->getMembership() === null) {
                        $this->priceCache[$id] = $qd->getPrice();
                        break;
                    }
                }
            }
        }

        return $this->priceCache[$id];
    }

    /**
     * Get product SKUs (multiple if there are variants)
     *
     * @param $product
     *
     * @return array
     */
    protected function getSkus($product)
    {
        return [$product->getSku()];
    }

    /**
     * Get "conditions" that can be used to restrict the results when searching.
     *
     * This is different from "attributes" which are used to construct full-fledged filters (CloudFilters).
     *
     * @param Product $product
     * @return array
     */
    protected function getProductConditions(Product $product)
    {
        return [
            'availability' => [$product->getEnabled() ? 'Y' : 'N'],
            'categories'   => $this->getAllCategoryIds($product),
            'type'         => [],
        ];
    }

    /**
     * Get searchable product attributes data
     *
     * @param $product
     *
     * @return array
     */
    protected function getSearchableProductAttributes(Product $product)
    {
        $attributes = [];

        $productAttributes = array_merge(
            $this->getProductAttributesOfSelectType($product),
            $this->getProductAttributesOfCheckboxType($product),
            $this->getProductAttributesOfTextareaType($product)
        );

        $activeLanguages = $this->getActiveLanguages();

        foreach ($productAttributes as $attr) {
            if (!isset($attributes[$attr['id']])) {
                $attributes[$attr['id']] = [];

                foreach ($activeLanguages as $lang) {
                    $attributes[$attr['id']]["name_$lang"] = htmlspecialchars_decode($attr["name_$lang"]);
                }
            }

            foreach ($activeLanguages as $lang) {
                if (!isset($attributes[$attr['id']]["values_$lang"])) {
                    $attributes[$attr['id']]["values_$lang"] = [];
                }

                $attributes[$attr['id']]["values_$lang"][] = htmlspecialchars_decode($attr["value_$lang"]);
            }
        }

        return array_values($attributes);
    }

    /**
     * Get filterable product attributes data
     *
     * @param $product
     *
     * @return array
     */
    protected function getFilterableProductAttributes(Product $product)
    {
        $productClassRepo = Database::getRepo('XLite\Model\ProductClass');

        $attributes = [];

        $productAttributes = array_merge(
            $this->getProductAttributesOfSelectType($product),
            $this->getProductAttributesOfCheckboxType($product)
        );

        $activeLanguages = $this->getActiveLanguages();

        foreach ($productAttributes as $attr) {
            if (!isset($attributes[$attr['id']])) {
                $group = $attr['productClassId'] !== null
                    ? $productClassRepo->find($attr['productClassId'])->getName() : null;

                $attributes[$attr['id']] = [
                    'id'                => $attr['id'],
                    'preselectAsFilter' => $this->isPreselectAttributeAsFilter($attr),
                    'group'             => $group,
                ];

                foreach ($activeLanguages as $lang) {
                    $attributes[$attr['id']]["name_$lang"] = htmlspecialchars_decode($attr["name_$lang"]);
                }
            }

            foreach ($activeLanguages as $lang) {
                if (!isset($attributes[$attr['id']]["values_$lang"])) {
                    $attributes[$attr['id']]["values_$lang"] = [];
                }

                $attributes[$attr['id']]["values_$lang"][] = htmlspecialchars_decode($attr["value_$lang"]);
            }
        }

        if (Config::getInstance()->General->show_out_of_stock_products !== 'directLink'
            && in_array($this->getProductStockStatus($product), [ProductRepo::INV_IN, ProductRepo::INV_LOW], true)
        ) {
            $attributes['availability'] = [
                'id'                => 'availability',
                'preselectAsFilter' => true,
                'group'             => null,
            ];

            foreach ($activeLanguages as $lang) {
                $attributes['availability']["name_$lang"] = (string) static::t('Availability', [], $lang);
                $attributes['availability']["values_$lang"] = [(string) static::t('In stock', [], $lang)];
            }
        }

        return array_values($attributes);
    }

    /**
     * Get "select"-type attributes with values
     *
     * @param $product
     *
     * @return mixed
     */
    protected function getProductAttributesOfSelectType(Product $product)
    {
        static $attributes = [];

        if (!isset($attributes[$product->getProductId()])) {
            $qb = Database::getEM()->createQueryBuilder()
                ->select('a.id')
                ->addSelect('IDENTITY(a.productClass) AS productClassId')
                ->from('XLite\Model\AttributeValue\AttributeValueSelect', 'av')
                ->join('av.attribute', 'a')
                ->leftJoin('av.attribute_option', 'ao')
                ->addSelect('ao.id AS optionId')
                ->where('av.product = :productId')
                ->setParameter('productId', $product->getProductId());

            foreach ($this->getInactiveFallbackLanguages() as $lang) {
                $qb
                    ->leftJoin('a.translations', "at_$lang", 'WITH', "at_$lang.code = :lng_$lang")
                    ->leftJoin('ao.translations', "aot_$lang", 'WITH', "aot_$lang.code = :lng_$lang")
                    ->setParameter("lng_$lang", $lang);
            }

            foreach ($this->getActiveLanguages() as $lang) {
                $qb
                    ->leftJoin('a.translations', "at_$lang", 'WITH', "at_$lang.code = :lng_$lang")
                    ->leftJoin('ao.translations', "aot_$lang", 'WITH', "aot_$lang.code = :lng_$lang")
                    ->setParameter("lng_$lang", $lang);

                $qb->addSelect("{$this->getFieldTranslationSqlExp('at', 'name', $lang)} AS name_$lang");

                $qb->addSelect("{$this->getFieldTranslationSqlExp('aot', 'name', $lang)} AS value_$lang");
            }

            $this->addProductAttributesQuerySelects($qb);

            $attributes[$product->getProductId()] = $qb
                ->getQuery()
                ->getArrayResult();
        }

        return $attributes[$product->getProductId()];
    }

    /**
     * Get "checkbox"-type attributes with values
     *
     * @param $product
     *
     * @return mixed
     */
    protected function getProductAttributesOfCheckboxType(Product $product)
    {
        static $attributes = [];

        if (!isset($attributes[$product->getProductId()])) {
            $qb = Database::getEM()->createQueryBuilder()
                ->select('a.id')
                ->addSelect('av.value')
                ->addSelect('IDENTITY(a.productClass) AS productClassId')
                ->from('XLite\Model\AttributeValue\AttributeValueCheckbox', 'av')
                ->join('av.attribute', 'a')
                ->where('av.product = :productId')
                ->setParameter('productId', $product->getProductId());

            $activeLanguages = $this->getActiveLanguages();

            foreach ($this->getInactiveFallbackLanguages() as $lang) {
                $qb
                    ->leftJoin('a.translations', "at_$lang", 'WITH', "at_$lang.code = :lng_$lang")
                    ->setParameter("lng_$lang", $lang);
            }

            foreach ($activeLanguages as $lang) {
                $qb
                    ->leftJoin('a.translations', "at_$lang", 'WITH', "at_$lang.code = :lng_$lang")
                    ->setParameter("lng_$lang", $lang);

                $qb->addSelect("{$this->getFieldTranslationSqlExp('at', 'name', $lang)} AS name_$lang");
            }

            $this->addProductAttributesQuerySelects($qb);

            $result = $qb
                ->getQuery()
                ->getArrayResult();

            foreach ($result as $k => $v) {
                foreach ($activeLanguages as $lang) {
                    $result[$k]["value_$lang"] = (string)static::t($v['value'] ? 'Yes' : 'No', [], $lang);
                }
            }

            $attributes[$product->getProductId()] = $result;
        }

        return $attributes[$product->getProductId()];
    }

    /**
     * Get "textarea"-type attributes with values
     *
     * @param $product
     *
     * @return mixed
     */
    protected function getProductAttributesOfTextareaType(Product $product)
    {
        $qb = Database::getEM()->createQueryBuilder()
            ->select('a.id')
            ->from('XLite\Model\AttributeValue\AttributeValueText', 'av')
            ->join('av.attribute', 'a')
            ->andWhere('av.product = :productId')
            ->andWhere('av.editable = :editable')
            ->setParameter('productId', $product->getProductId())
            ->setParameter('editable', false);

        foreach ($this->getInactiveFallbackLanguages() as $lang) {
            $qb
                ->leftJoin('a.translations', "at_$lang", 'WITH', "at_$lang.code = :lng_$lang")
                ->leftJoin('av.translations', "avt_$lang", 'WITH', "avt_$lang.code = :lng_$lang")
                ->setParameter("lng_$lang", $lang);
        }

        foreach ($this->getActiveLanguages() as $lang) {
            $qb
                ->leftJoin('a.translations', "at_$lang", 'WITH', "at_$lang.code = :lng_$lang")
                ->leftJoin('av.translations', "avt_$lang", 'WITH', "avt_$lang.code = :lng_$lang")
                ->setParameter("lng_$lang", $lang);

            $qb->addSelect("{$this->getFieldTranslationSqlExp('at', 'name', $lang)} AS name_$lang");

            $qb->addSelect("{$this->getFieldTranslationSqlExp('avt', 'value', $lang)} AS value_$lang");
        }

        $this->addProductAttributesQuerySelects($qb);

        $result = $qb
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    /**
     * Construct an expression in the form of IFNULL(a1, IFNULL(a2, ...)) out of field names
     *
     * @param $fieldNames
     *
     * @return string
     */
    protected function getIfNullChainSqlExp($fieldNames)
    {
        $fieldName = array_shift($fieldNames);

        return empty($fieldNames)
            ? $fieldName
            : "IFNULL($fieldName, " . $this->getIfNullChainSqlExp($fieldNames) . ')';
    }

    protected function getFieldTranslationSqlExp($tblPrefix, $fieldName, $lang)
    {
        $languages = array_unique(
            array_merge(
                [$lang],
                $this->getFallbackLanguages(),
                $this->getActiveLanguages()
            )
        );

        $fieldNames = [];

        foreach ($languages as $language) {
            $fieldNames[] = "{$tblPrefix}_$language.$fieldName";
        }

        return $this->getIfNullChainSqlExp($fieldNames);
    }

    /**
     * Override to modify QueryBuilder before querying attributes
     *
     * @param $qb
     */
    protected function addProductAttributesQuerySelects($qb)
    {
    }

    /**
     * Check if specific attribute should be preselected as a custom filter for CloudFilters
     *
     * @param $attribute
     *
     * @return bool
     */
    protected function isPreselectAttributeAsFilter($attribute)
    {
        return $attribute['productClassId'] !== null;
    }

    /**
     * Get product variants data.
     * If ProductVariants is disabled, then there will be a single product variant representing the main product.
     *
     * @param Product $product
     * @param         $attributes
     *
     * @return array
     */
    protected function getProductVariants(Product $product, $attributes)
    {
        $variant = [
            'id'           => $product->getId(),
            'price'        => $this->getProductPrice($product),
            'attributes'   => $attributes,
            'stock_status' => $this->getProductStockStatus($product),
        ];

        return [$variant];
    }

    /**
     * Get product stock status
     *
     * @param Product $product
     *
     * @return string
     */
    protected function getProductStockStatus(Product $product)
    {
        if (!$product->getInventoryEnabled()) {
            return ProductRepo::INV_IN;
        }

        if ($product->getPublicAmount() <= 0) {
            return ProductRepo::INV_OUT;
        }

        return $product->getPublicAmount() < $product->getLowLimitAmount()
            ? ProductRepo::INV_LOW
            : ProductRepo::INV_IN;
    }

    /**
     * Get additional meta information about the product
     *
     * @param $product
     *
     * @return array
     */
    protected function getProductMetaInfo(Product $product)
    {
        $activeLanguages = $this->getActiveLanguages();

        $translations = [];
        foreach ($product->getTranslations() as $t) {
            if (isset($translations[$t->getCode()])) {
                continue;
            }

            $translations[$t->getCode()] = [
                'description'      => $t->getDescription(),
                'briefDescription' => $t->getBriefDescription(),
                'metaTags'         => $t->getMetaTags(),
                'metaDesc'         => $t->getMetaDesc(),
            ];
        }

        foreach ($activeLanguages as $lang) {
            $translations[$lang] = $this->getFieldTranslation($translations, $lang);
        }

        $info = [];

        foreach ($activeLanguages as $lang) {
            $info["name_$lang"]   = '_meta_additional_';
            $info["values_$lang"] = [
                $translations[$lang]['metaTags'],
                $product->getTranslatedMetaDesc($translations[$lang]),
            ];

            // Include brief description if full description is not empty (so that both will be indexed)
            if ($translations[$lang]['description']) {
                $info["values_$lang"][] = $translations[$lang]['briefDescription'];
            }
        }

        return $info;
    }

    /**
     * Get product category data
     *
     * @param $product
     *
     * @return array
     */
    protected function getProductCategoryData(Product $product)
    {
        $categories = [];

        $catRepo = Database::getRepo('XLite\Model\Category');

        $activeLanguages = $this->getActiveLanguages();
        $defaultLanguage = $this->getDefaultCustomerLanguage();

        /** @var Category $category */
        foreach ($product->getCategories() as $category) {
            $id = $category->getCategoryId();

            if (!isset($this->categoryCache[$id])) {
                $categoryPath = $catRepo->getCategoryPath($id);

                $enabledPath = true;
                $path        = [];

                /** @var Category $parent */
                foreach ($categoryPath as $parent) {
                    $nameTranslations = [];
                    foreach ($parent->getTranslations() as $t) {
                        if (isset($nameTranslations[$t->getCode()])) {
                            continue;
                        }

                        $nameTranslations[$t->getCode()] = htmlspecialchars_decode($t->getName());
                    }

                    foreach ($activeLanguages as $lang) {
                        if (!isset($path["path_$lang"])) {
                            $path["path_$lang"] = [];
                        }

                        $path["path_$lang"][] = [
                            'id'   => $parent->getCategoryId(),
                            'name' => $this->getFieldTranslation($nameTranslations, $lang),
                            'url'  => $this->getItemUrl('category', ['category_id' => $parent->getCategoryId()]),
                        ];
                    }

                    if (!$parent->getEnabled()) {
                        $enabledPath = false;
                    }
                }

                $this->categoryCache[$id] = [
                        'id'      => $id,
                        'enabled' => $enabledPath,
                    ] + $path;
            }

            if (!empty($this->categoryCache[$id]["path_$defaultLanguage"]) && $this->categoryCache[$id]['enabled']) {
                $categories[] = $this->categoryCache[$id];
            }
        }

        return ['category' => $categories];
    }

    /**
     * Get product category ids
     *
     * @param $product
     *
     * @return array
     */
    protected function getAllCategoryIds(Product $product)
    {
        $ids = [];

        $defaultLanguage = $this->getDefaultCustomerLanguage();

        /** @var Category $category */
        foreach ($product->getCategories() as $category) {
            $id = $category->getCategoryId();

            if (!empty($this->categoryCache[$id])) {
                $ids = array_merge($ids, !empty($this->categoryCache[$id]["path_$defaultLanguage"])
                    ? array_map(function ($c) {
                        return $c['id'];
                    }, $this->categoryCache[$id]["path_$defaultLanguage"])
                    : [$id]);
            }
        }

        return $ids;
    }

    /**
     * Get product image
     *
     * @param $product
     *
     * @return array
     */
    protected function getProductImage(Product $product)
    {
        $result = [];

        if ($product->getImage()) {
            list(
                $result['image_width'],
                $result['image_height'],
                $result['image_src']
                ) = $product->getImage()->getResizedURL(static::MAX_THUMBNAIL_WIDTH, static::MAX_THUMBNAIL_HEIGHT);
        }

        return $result;
    }

    protected function formatProductMultiLingualFields(&$data)
    {
        foreach ($this->getActiveLanguages() as $lang) {
            $data["modifiers_$lang"] = [];

            foreach ($data['modifiers'] as $modifier) {
                $data["modifiers_$lang"][] = [
                    'name'   => $modifier["name_$lang"],
                    'values' => $modifier["values_$lang"],
                ];
            }

            $data["sort_str_name_$lang"] = $data["name_$lang"];
        }

        unset($data['modifiers']);
    }

    /**
     * Get categories data
     *
     * @param $params
     *
     * @return array
     */
    public function getCategories($params)
    {
        $repo = Database::getRepo('XLite\Model\Category');

        $cnd = $this->getCategorySearchConditions($params);

        $categories = $repo->search($cnd, false);

        $categoriesArray = [];

        $rootCatId = Database::getRepo('XLite\Model\Category')->getRootCategoryId();

        $activeLanguages = $this->getActiveLanguages();

        foreach ($categories as $category) {
            $parentId = $category->getParentId() == $rootCatId ? 0 : $category->getParentId();

            $categoryHash = [
                    'id'               => $category->getCategoryId(),
                    'parent'           => $parentId,
                    'category_id_path' => [],
                ] + $this->getCategoryTranslations($category);

            if ($category->getImage()) {
                list($categoryHash['image_width'], $categoryHash['image_height'], $categoryHash['image_src']) =
                    $category->getImage()->getResizedURL(static::MAX_THUMBNAIL_WIDTH, static::MAX_THUMBNAIL_HEIGHT);
            }

            $categoryHash['url'] = $this->getItemUrl('category', ['category_id' => $category->getCategoryId()]);

            $path = $repo->getCategoryPath($category->getCategoryId());

            $categoryHash['path'] = [];

            /** @var Category $c */
            foreach ($path as $c) {
                if ($c->getCategoryId() === $category->getCategoryId()) {
                    break;
                }

                $categoryPathNode = [
                    'id'  => $c->getCategoryId(),
                    'url' => $this->getItemUrl('category', ['category_id' => $c->getCategoryId()]),
                ];

                $cTranslations = [];
                foreach ($c->getTranslations() as $t) {
                    if (isset($cTranslations[$t->getCode()])) {
                        continue;
                    }

                    $cTranslations[$t->getCode()] = $t->getName();
                }

                foreach ($activeLanguages as $lang) {
                    $categoryPathNode["name_$lang"] = $this->getFieldTranslation($cTranslations, $lang);
                }

                $categoryHash['path'][] = $categoryPathNode;

                $categoryHash['category_id_path'][] = $c->getCategoryId();
            }

            $this->formatCategoryMultiLingualFields($categoryHash);

            $categoriesArray[] = $categoryHash;
        }

        return $categoriesArray;
    }

    protected function getCategoryTranslations(Category $category)
    {
        $data = [];

        $categoryTranslations = [];
        foreach ($category->getTranslations() as $t) {
            if (isset($categoryTranslations[$t->getCode()])) {
                continue;
            }

            $categoryTranslations[$t->getCode()] = [
                'name'        => htmlspecialchars_decode($t->getName()),
                'description' => $category::getPreprocessedValue($t->getDescription()) ?: $t->getDescription(),
            ];
        }

        foreach ($this->getActiveLanguages() as $lang) {
            $data["name_$lang"]        = $this->getFieldTranslation($categoryTranslations, $lang, 'name');
            $data["description_$lang"] = $this->getFieldTranslation($categoryTranslations, $lang, 'description');
        }

        return $data;
    }

    protected function formatCategoryMultiLingualFields(&$data)
    {
        foreach ($this->getActiveLanguages() as $lang) {
            $data["path_$lang"] = [];

            foreach ($data['path'] as $path) {
                $data["path_$lang"][] = [
                    'id'   => $path['id'],
                    'url'  => $path['url'],
                    'name' => $path["name_$lang"],
                ];
            }
        }

        unset($data['path']);
    }

    /**
     * Get categories data
     *
     * @param $start
     * @param $limit
     *
     * @return array
     */
    public function getPages($start, $limit)
    {
        $repo = Database::getRepo('\XLite\Module\CDev\SimpleCMS\Model\Page');

        $pagesArray = [];

        if ($repo) {
            $cnd = $this->getPageSearchConditions();

            $cnd->{PageRepo::P_LIMIT}       = [$start, $limit];
            $cnd->{PageRepo::PARAM_ENABLED} = true;
            $pages                          = $repo->search($cnd, false);

            foreach ($pages as $page) {
                $url = $this->getItemUrl('page', ['id' => $page->getId()]);

                $pageHash = [
                        'id'  => $page->getId(),
                        'url' => $url,
                    ] + $this->getPageTranslations($page);

                $pagesArray[] = $pageHash;
            }
        }

        return $pagesArray;
    }

    protected function getPageTranslations(\XLite\Module\CDev\SimpleCMS\Model\Page $page)
    {
        $data = [];

        $pageTranslations = [];
        foreach ($page->getTranslations() as $t) {
            if (isset($pageTranslations[$t->getCode()])) {
                continue;
            }

            $pageTranslations[$t->getCode()] = [
                'name' => $t->getName(),
                'body' => $t->getBody(),
            ];
        }

        foreach ($this->getActiveLanguages() as $lang) {
            $data["title_$lang"]   = $this->getFieldTranslation($pageTranslations, $lang, 'name');
            $data["content_$lang"] = $this->getFieldTranslation($pageTranslations, $lang, 'body');
        }

        return $data;
    }

    /**
     * Check if current store is running in multi-domain mode
     *
     * @return bool
     */
    protected function isMultiDomain()
    {
        return Main::isMultiDomain();
    }

    /**
     * Get product/category/page url
     *
     * @param $type
     * @param $params
     *
     * @return string
     */
    protected function getItemUrl($type, $params)
    {
        $url = Converter::buildFullURL($type, '', $params, XLite::getCustomerScript(), null, true);

        if ($this->isMultiDomain() || Main::isXCCloud()) {
            // Use domain-agnostic URL for multi-domain stores
            $path = parse_url($url, PHP_URL_PATH);

            $query = parse_url($url, PHP_URL_QUERY);

            return $path . ($query ? '?' . $query : '');
        } else {
            return $url;
        }
    }
}
