<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Admin;


/**
 * Products list controller
 */
class ProductList extends \XLite\Controller\Admin\ProductList implements \XLite\Base\IDecorator
{
    /**
     * Do action clone
     *
     * @return void
     */
    protected function doActionSaleCancelSale()
    {
        $select = \XLite\Core\Request::getInstance()->select;
        if ($select && is_array($select)) {
            $this->cancelSaleByIds(array_keys($this->getSelected()));
            \XLite\Core\TopMessage::addInfo('Products information has been successfully updated');

        } elseif ($ids = $this->getActionProductsIds()) {
            $this->cancelSaleByIds($ids);
            \XLite\Core\TopMessage::addInfo('Products information has been successfully updated');

        } else {
           \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    /**
     * Cancel sale for provided product ids
     *
     * @param $ids
     */
    protected function cancelSaleByIds($ids)
    {
        $qb = \XLite\Core\Database::getRepo('XLite\Model\Product')->createQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->update('\XLite\Model\Product', $alias)
            ->set("{$alias}.participateSale", $qb->expr()->literal(false))
            ->andWhere($qb->expr()->in("{$alias}.product_id", $ids))
            ->execute();
    }
}
