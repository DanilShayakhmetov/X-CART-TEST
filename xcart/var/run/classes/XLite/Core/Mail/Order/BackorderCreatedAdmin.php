<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;


use XLite\Model\Order;
use XLite\Model\Product;

class BackorderCreatedAdmin extends \XLite\Core\Mail\Order\AAdmin
{
    static function getDir()
    {
        return 'backorder_created';
    }

    protected static function defineVariables()
    {
        return [
                'backordered_item_names' => '',
            ] + parent::defineVariables();
    }

    public function __construct(Order $order)
    {
        parent::__construct($order);

        $backorderData = self::getBackorderData($order);

        $this->populateVariables([
            'backordered_item_names' => implode(', ', $backorderData['items']),
        ]);

        $this->appendData([
            'backorderedProducts'   => $backorderData['products'],
            'product_url_processor' => [static::class, 'productUrlProcessor'],
        ]);
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    public static function getBackorderData(Order $order) {
        $backorderedItems = [];
        $backorderedProducts = [];

        foreach ($order->getItems() as $item) {
            if (0 < $item->getBackorderedAmount()) {
                $backorderedItems[]    = $item->getName();
                $backorderedProducts[] = $item->getProduct();
            }
        }

        return ['items' => $backorderedItems, 'products' => $backorderedProducts];
    }


    /**
     * @param Product $product
     *
     * @return string
     */
    public static function productUrlProcessor(Product $product)
    {
        return \XLite\Core\Converter::buildFullURL('product', '', [
            'product_id' => $product->getProductId(),
            'page'       => 'inventory'
        ], \XLite::getAdminScript());
    }
}