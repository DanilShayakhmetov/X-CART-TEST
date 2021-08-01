<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormModel\Product;

use XLite\Core\Database;

/**
 * @Decorator\Before("CDev\Wholesale")
 */
class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getWholesalePricesUrl()
    {
        $identity = $this->getDataObject()->default->identity;

        return $this->buildURL(
            'product',
            '',
            [
                'product_id' => $identity,
                'page'       => 'wholesale_pricing',
            ]
        );
    }
}
