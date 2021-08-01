<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\ItemsList\Model\Shipping;

/**
 * Shipping methods list
 */
 class Methods extends \XLite\View\ItemsList\Model\Shipping\MethodsAbstract implements \XLite\Base\IDecorator
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $carrierParam = \XLite\Model\Repo\Shipping\Method::P_CARRIER;

        $config = \XLite\Core\Config::getInstance()->CDev->USPS;
        if (!empty($result->{$carrierParam})
            && 'usps' === $result->{$carrierParam}
            && $config->dataProvider === 'pitneyBowes'
        ) {
            $result->{$carrierParam} = 'pb_usps';
        }

        return $result;
    }
}
