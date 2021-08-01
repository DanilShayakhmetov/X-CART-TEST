<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Admin;

/**
 * Sale discounts
 */
abstract class Promotions extends \XLite\Module\CDev\VolumeDiscounts\Controller\Admin\Promotions implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_SALE_DISCOUNTS = 'sale_discounts';

    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = parent::getPagesStatic();
        $list[static::PAGE_SALE_DISCOUNTS] = array(
            'name' => static::t('Sale'),
            'tpl'  => 'modules/CDev/Sale/sale_discounts/body.twig',
            'permission' => 'manage sale discounts',
        );

        return $list;
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || (static::PAGE_SALE_DISCOUNTS === $this->getPage()
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage sale discounts')
            );
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionSaleDiscountsUpdate()
    {
        $list = new \XLite\Module\CDev\Sale\View\ItemsList\SaleDiscounts();
        $list->processQuick();
    }
}
