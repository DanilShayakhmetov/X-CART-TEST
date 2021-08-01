<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Controller\Admin;


/**
 * Products list controller
 */
 class ProductList extends \XLite\Module\XC\GoogleFeed\Controller\Admin\ProductList implements \XLite\Base\IDecorator
{
    /**
     * Enable feed for products
     *
     * @return void
     */
    protected function doActionFacebookProductFeedEnable()
    {
        $select = \XLite\Core\Request::getInstance()->select;
        if ($select && is_array($select)) {
            $data = array_fill_keys(
                array_keys($this->getSelected()),
                ['facebookMarketingEnabled' => true]
            );

            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($data);
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }
    /**
     * Disable feed for products
     *
     * @return void
     */
    protected function doActionFacebookProductFeedDisable()
    {
        $select = \XLite\Core\Request::getInstance()->select;
        if ($select && is_array($select)) {
            $data = array_fill_keys(
                array_keys($this->getSelected()),
                ['facebookMarketingEnabled' => false]
            );

            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($data);
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }
}