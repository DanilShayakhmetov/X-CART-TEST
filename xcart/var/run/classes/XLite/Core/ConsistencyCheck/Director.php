<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck;

use XLite\Core\ConsistencyCheck\Rules\AttributeValue\AttributeValueSelect\AttributeOptionExistsRule;
use XLite\Core\ConsistencyCheck\Rules\Category\RootRule;
use XLite\Core\ConsistencyCheck\Rules\CleanURL\DuplicateRule;
use XLite\Core\ConsistencyCheck\Rules\Order\PaymentStatusExistsRule;
use XLite\Core\ConsistencyCheck\Rules\Order\ProfileExistsRule;
use XLite\Core\ConsistencyCheck\Rules\Order\ShippingStatusExistsRule;
use XLite\Core\ConsistencyCheck\Rules\OrderItem\OwnerRule;
use XLite\Core\ConsistencyCheck\Rules\Surcharges\OrderItemSurchargesRule;
use XLite\Core\ConsistencyCheck\Rules\Surcharges\OrderSurchargesRule;
use XLite\Core\Database;

class Director
{
    /**
     * @return array
     */
    public function getRetrievers()
    {
        return [
            'categories' => [
                'name'      => 'Categories',
                'retriever' => new Retriever($this->getCategoriesRules()),
            ],
            'clean_urls' => [
                'name'      => 'Clean URL',
                'retriever' => new Retriever($this->getCleanURLRules()),
            ],
            'surcharges' => [
                'name'      => 'Surcharges',
                'retriever' => new Retriever($this->getSurchargesRules()),
            ],
            'order_items' => [
                'name'      => 'Order items',
                'retriever' => new Retriever($this->getOrderItemsRules()),
            ],
            'orders' => [
                'name'      => 'Orders',
                'retriever' => new Retriever($this->getOrdersRules()),
            ],
            'attribute_values_select' => [
                'name'      => 'Attribute values (select)',
                'retriever' => new Retriever($this->getAttirbuteValueSelectRules()),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getCategoriesRules()
    {
        return [
            'root_category_check' => new RootRule(
                Database::getRepo('XLite\Model\Category')
            ),
        ];
    }

    /**
     * @return array
     */
    protected function getCleanURLRules()
    {
        return [
            'duplicates' => new DuplicateRule(
                Database::getRepo('XLite\Model\CleanURL')
            ),
        ];
    }

    /**
     * @return array
     */
    protected function getSurchargesRules()
    {
        return [
            'order_surcharges' => new OrderSurchargesRule(
                Database::getRepo('XLite\Model\Order\Surcharge')
            ),
            'order_item_surcharges' => new OrderItemSurchargesRule(
                Database::getRepo('XLite\Model\OrderItem\Surcharge')
            ),
        ];
    }

    /**
     * @return array
     */
    protected function getOrderItemsRules()
    {
        return [
            'order_items' => new OwnerRule(
                Database::getRepo('XLite\Model\OrderItem')
            ),
        ];
    }

    /**
     * @return array
     */
    protected function getOrdersRules()
    {
        return [
            'profile_exists' => new ProfileExistsRule(
                Database::getRepo('XLite\Model\Order')
            ),
            'shipping_status_exists' => new ShippingStatusExistsRule(
                Database::getRepo('XLite\Model\Order')
            ),
            'payment_status_exists' => new PaymentStatusExistsRule(
                Database::getRepo('XLite\Model\Order')
            ),
        ];
    }

    /**
     * @return array
     */
    protected function getAttirbuteValueSelectRules()
    {
        return [
            'option_exists' => new AttributeOptionExistsRule(
                Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect')
            ),
        ];
    }
}
