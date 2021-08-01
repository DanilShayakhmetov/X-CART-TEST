<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\Config;
use XLite\Model\Attribute;
use XLite\Model\AttributeValue\AttributeValueCheckbox;
use XLite\Model\Product;
use XLite\Module\QSL\CloudSearch\Main;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;
use XLite\Module\XC\ProductVariants\Model\AttributeValue\AttributeValueSelect;
use XLite\Module\XC\ProductVariants\Model\ProductVariant;

/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"XC\ProductVariants"})
 */
abstract class StoreApiProductVariants extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    protected $attributeCache = [];

    /**
     * Get product variants data.
     *
     * @param Product $product
     * @param         $attributes
     *
     * @return array
     */
    protected function getProductVariants(Product $product, $attributes)
    {
        $variants = [];

        $activeLanguages = $this->getActiveLanguages();

        if ($product->getVariants()->count() > 0) {
            $variantAttrIds = array_map(function ($v) {
                return $v->getAttribute()->getId();
            }, $product->getVariants()->first()->getValues());

            $commonAttrs = array_filter($attributes, function ($attr) use ($variantAttrIds) {
                return !in_array($attr['id'], $variantAttrIds) && $attr['id'] !== 'availability';
            });

            /** @var ProductVariant $variant */
            foreach ($product->getVariants() as $variant) {
                if ($variant->isOutOfStock() && !Main::isAdminSearchEnabled()) {
                    continue;
                }

                $variantData = [
                    'id'           => $variant->getId(),
                    'price'        => $variant->getDisplayPrice(),
                    'attributes'   => [],
                    'stock_status' => $this->getVariantStockStatus($product, $variant),
                ];

                foreach ($variant->getValues() as $value) {
                    $attrValues = [];

                    if ($value instanceof AttributeValueSelect) {
                        $valueTranslations = [];
                        foreach ($value->getAttributeOption()->getTranslations() as $t) {
                            if (isset($valueTranslations[$t->getCode()])) {
                                continue;
                            }

                            $valueTranslations[$t->getCode()] = $t->getName();
                        }

                        foreach ($activeLanguages as $lang) {
                            $attrValues["values_$lang"] = [$this->getFieldTranslation($valueTranslations, $lang)];
                        }

                    } else if ($value instanceof AttributeValueCheckbox) {
                        foreach ($activeLanguages as $lang) {
                            $attrValues["values_$lang"] = [
                                (string)static::t($value->getValue() ? 'Yes' : 'No', [], $lang),
                            ];
                        }

                    } else {
                        continue;
                    }

                    $attrData = $this->getAttributeData($value->getAttribute()->getId(), $attributes);

                    $variantData['attributes'][] = array_merge($attrData, $attrValues);
                }

                if (Config::getInstance()->General->show_out_of_stock_products !== 'directLink'
                    && in_array($this->getVariantStockStatus($product, $variant), [ProductRepo::INV_IN, ProductRepo::INV_LOW], true)
                ) {
                    $availabilityAttr = [
                        'id'                => 'availability',
                        'preselectAsFilter' => true,
                        'group'             => null,
                    ];

                    foreach ($activeLanguages as $lang) {
                        $availabilityAttr["name_$lang"] = (string) static::t('Availability', [], $lang);
                        $availabilityAttr["values_$lang"] = [(string) static::t('In stock', [], $lang)];
                    }

                    $variantData['attributes'][] = $availabilityAttr;
                }

                $variantData['attributes'] = array_merge($variantData['attributes'], $commonAttrs);

                $variants[] = $variantData;
            }

            if (empty($variants)) {
                // Return a fake variant if all existing variants are out of stock to allow filtering on regular (non-variant) attributes. This keeps behavior consistent with regular products.

                return parent::getProductVariants($product, $commonAttrs);
            }

        } else {
            return parent::getProductVariants($product, $attributes);
        }

        return $variants;
    }

    /**
     * Get product variant stock status
     *
     * @param Product        $product
     * @param ProductVariant $variant
     *
     * @return string
     */
    protected function getVariantStockStatus(Product $product, ProductVariant $variant)
    {
        if (!$product->getInventoryEnabled()) {
            return ProductRepo::INV_IN;
        }

        if ($variant->getPublicAmount() <= 0) {
            return ProductRepo::INV_OUT;
        }

        return $variant->getPublicAmount() < $product->getLowLimitAmount()
            ? ProductRepo::INV_LOW
            : ProductRepo::INV_IN;
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
        if ($product->getVariants()->count() > 0) {
            return $product->getDisplayPrice();
        }

        return parent::getProductPrice($product);
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
        $skus = parent::getSkus($product);

        foreach ($product->getVariants() as $variant) {
            $skus[] = $variant->getSku();
        }

        return $skus;
    }

    /**
     * Find attribute by id in the $attributes array
     *
     * @param $id
     * @param $attributes
     *
     * @return array
     */
    protected function getAttributeData($id, $attributes)
    {
        if (!isset($this->attributeCache[$id])) {
            foreach ($attributes as $attribute) {
                if ($id === $attribute['id']) {
                    $this->attributeCache[$id] = $attribute;
                    break;
                }
            }
        }

        return $this->attributeCache[$id];
    }
}
