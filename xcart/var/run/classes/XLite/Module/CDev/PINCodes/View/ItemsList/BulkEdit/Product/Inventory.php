<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\ItemsList\BulkEdit\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
abstract class Inventory extends \XLite\Module\XC\BulkEditing\View\ItemsList\BulkEdit\Product\InventoryAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/PINCodes/items_list/bulk_edit/selected/style.less';

        return $list;
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);

        if ($entity->hasManualPinCodes()) {
            $classes[] = 'has-manual-pin-codes';
        }

        return $classes;
    }
}
