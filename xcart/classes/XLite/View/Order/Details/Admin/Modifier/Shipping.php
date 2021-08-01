<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin\Modifier;

/**
 * Shipping modifier widget
 */
class Shipping extends \XLite\View\Order\Details\Admin\Modifier
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        /** @var $shippingModifier \XLite\Logic\Order\Modifier\Shipping */
        $shippingModifier = $this->getOrder()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        $cost = $this->getOrder()->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);

        $shippable = false;
        foreach ($this->getOrder()->getItems() as $item) {
            if ($item->isShippable() && !$item->isDeleted()) {
                $shippable = true;
            }
        }

        $result = $shippable
            || ($shippingModifier && $shippingModifier->canApply() && ($shippingModifier->getSelectedRate() || $cost > 0));

        return parent::isVisible() && $result;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/page/parts/totals.modifier.shipping.twig';
    }
}
