<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;

class Product
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    public static function getDataByOrderItem(\XLite\Model\OrderItem $item): array
    {
        $image = $item->getImage() ? $item->getImageURL() : null;

        $data = [
            'id'          => (string) $item->getItemId(),
            'title'       => $item->getName() ?: '',
            'url'         => '',
            'description' => '',
            'vendor'      => 'admin',
            'image_url'   => $image ?: '',
            'variants'    => static::getVariantsByOrderItemData($item),
        ];

        if ($item->getObject()) {
            // Replace with product data if available
            $data = array_merge(
                $data,
                static::getDataByProduct($item->getObject())
            );
        }

        return $data;
    }

    /**
     * @param \XLite\Model\Product|\XLite\Module\XC\MailChimp\Model\Product $product
     *
     * @return array
     */
    public static function getDataByProduct(\XLite\Model\Product $product): array
    {
        return [
            'id'                   => (string) $product->getProductId(),
            'title'                => $product->getName() ?: '',
            'url'                  => $product->getFrontURLForMailChimp(),
            'description'          => $product->getBriefDescription() ?: '',
            'vendor'               => 'admin',
            'image_url'            => $product->getImageURL() ?: '',
            'variants'             => static::getVariantsByProductData($product),
        ];
    }

    /**
     * @param \XLite\Model\Product|\XLite\Module\XC\MailChimp\Model\Product $product
     *
     * @return array
     */
    protected static function getVariantsByProductData(\XLite\Model\Product $product): array
    {
        return [
            Variant::getVariantDataByProduct($product),
        ];
    }

    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    protected static function getVariantsByOrderItemData(\XLite\Model\OrderItem $item): array
    {
        return [
            Variant::getVariantDataByOrderItem($item),
        ];
    }
}