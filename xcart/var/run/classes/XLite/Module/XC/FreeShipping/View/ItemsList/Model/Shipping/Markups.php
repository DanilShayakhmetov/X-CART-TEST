<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\ItemsList\Model\Shipping;

/**
 * Shipping rates list
 */
 class Markups extends \XLite\View\ItemsList\Model\Shipping\MarkupsAbstract implements \XLite\Base\IDecorator
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        /** @var \XLite\Model\Shipping\Method $entity */
        $entity = $this->getModelForm()->getModelObject();

        return parent::isVisible() && !$entity->getFree() && !$entity->isFixedFee();
    }
}
