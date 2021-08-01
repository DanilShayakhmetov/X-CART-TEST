<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Controller\Customer;

/**
 * ChangeAttributeValues page controller extension
 */
class ChangeAttributeValues extends \XLite\Controller\Customer\ChangeAttributeValues implements \XLite\Base\IDecorator
{
    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        $product = parent::getProduct();
        $product->setWholesaleQuantity($this->getItem()->getAmount());

        return $product;
    }


    /**
     * Show message about wrong product amount
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return void
     */
    protected function processInvalidAmountError(\XLite\Model\OrderItem $item)
    {
        if ($item->hasWrongMinQuantity()) {
            \XLite\Core\TopMessage::addWarning(
                'The minimum amount of "{{product}}" product {{description}} allowed to purchase is {{min}} item(s). Please adjust the product quantity.',
                [
                    'product'     => $item->getProduct()->getName(),
                    'description' => $item->getExtendedDescription(),
                    'min'         => $item->getMinQuantity()
                ]
            );

        } else {
            parent::processInvalidAmountError($item);
        }
    }
}
