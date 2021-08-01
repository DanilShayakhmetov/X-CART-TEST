<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core;

use XLite\Module\XC\CanadaPost\Core\Mail\ProductsReturnApproved;
use XLite\Module\XC\CanadaPost\Core\Mail\ProductsReturnRejected;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * Send mail notification to customer that his products return has been approved
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Canada Post products return
     *                                                                 model
     */
    public static function sendProductsReturnApproved(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return)
    {
        (new ProductsReturnApproved($return))->schedule();
    }

    /**
     * Send mail notification to customer that his products return has been rejected
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Canada Post products return
     *                                                                 model
     */
    public static function sendProductsReturnRejected(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return)
    {
        (new ProductsReturnRejected($return))->schedule();
    }
}
