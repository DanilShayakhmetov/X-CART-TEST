<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\XC\ProductVariants\Controller\Admin;


/**
 * Products list controller
 *
 * @Decorator\Depend({"CDev\Sale","XC\ProductVariants"})
 */
class ProductList extends \XLite\Controller\Admin\ProductList implements \XLite\Base\IDecorator
{
    /**
     * Cancel sale for provided product ids
     *
     * @param $ids
     */
    protected function cancelSaleByIds($ids)
    {
        parent::cancelSaleByIds($ids);

        $qb = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->createQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->update('\XLite\Module\XC\ProductVariants\Model\ProductVariant', $alias)
            ->set("{$alias}.defaultSale", $qb->expr()->literal(true))
            ->set("{$alias}.salePriceValue", 0)
            ->andWhere($qb->expr()->in("{$alias}.product", $ids))
            ->execute();
    }
}
