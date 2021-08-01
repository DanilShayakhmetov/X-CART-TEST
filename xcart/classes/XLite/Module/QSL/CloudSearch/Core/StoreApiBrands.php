<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"QSL\ShopByBrand"})
 */
abstract class StoreApiBrands extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Get total brand count
     *
     * @return int
     */
    protected function getBrandsCount()
    {
        return Database::getRepo('XLite\Module\QSL\ShopByBrand\Model\Brand')->countEnabledBrands();
    }

    /**
     * Get products data
     *
     * @return array
     */
    public function getBrands()
    {
        $result = Database::getRepo('XLite\Module\QSL\ShopByBrand\Model\Brand')->getCategoryBrandsWithProductCount();

        return array_map([$this, 'getBrand'], $result);
    }

    /**
     * Get brand details from Brand model
     *
     * @param $record
     * @return array
     */
    protected function getBrand($record)
    {
        $brand = $record[0];

        return [
            'id'  => $brand->getBrandId(),
            'url' => $this->getBrandUrl($brand),
        ] + $this->getBrandTranslations($brand);
    }

    protected function getBrandTranslations(\XLite\Module\QSL\ShopByBrand\Model\Brand $brand)
    {
        $data = [];

        $brandTranslations = [];
        foreach ($brand->getTranslations() as $t) {
            if (isset($brandTranslations[$t->getCode()])) {
                continue;
            }

            $brandTranslations[$t->getCode()] = [
                'description' => $t->getDescription(),
            ];
        }

        if ($brand->getOption()) {
            foreach ($brand->getOption()->getTranslations() as $t) {
                if (!isset($brandTranslations[$t->getCode()])) {
                    $brandTranslations[$t->getCode()] = [];
                }

                if (isset($brandTranslations[$t->getCode()]['name'])) {
                    continue;
                }

                $brandTranslations[$t->getCode()]['name'] = $t->getName();
            }
        }

        foreach ($this->getActiveLanguages() as $lang) {
            $data["name_$lang"]        = $this->getFieldTranslation($brandTranslations, $lang, 'name');
            $data["description_$lang"] = $this->getFieldTranslation($brandTranslations, $lang, 'description');
        }

        return $data;
    }

    /**
     * Get brand URL
     *
     * @param $brand
     * @return string
     */
    protected function getBrandUrl($brand)
    {
        $url = Converter::buildFullURL(
            'brand', '', ['brand_id' => $brand->getBrandId()]
        );

        return $this->getItemUrl($url);
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
        $conditions = parent::getProductConditions($product);

        $brandAttrId = $this->getBrandAttributeId();

        foreach ($this->getProductAttributesOfSelectType($product) as $attr) {
            if ($attr['id'] === $brandAttrId) {
                $brandId = $this->getBrandIdByAttributeOptionId($attr['optionId']);

                $conditions['brand'] = [$brandId];
            }
        }

        return $conditions;
    }

    /**
     * Get Brand attribute id
     *
     * @return null
     */
    protected function getBrandAttributeId()
    {
        $attribute = Database::getRepo('XLite\Model\Attribute')->findBrandAttribute();

        return $attribute ? $attribute->getId() : null;
    }

    /**
     * @param $optionId
     * @return null
     */
    protected function getBrandIdByAttributeOptionId($optionId)
    {
        static $brands = [];

        if (!isset($brands[$optionId])) {
            $brand = Database::getRepo('XLite\Module\QSL\ShopByBrand\Model\Brand')->findOneByOption($optionId);

            $brands[$optionId] = $brand !== null ? $brand->getId() : null;
        }

        return $brands[$optionId];
    }
}
