<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

/**
 * Product details page extension
 */
abstract class Details extends \XLite\View\Product\Details\Customer\Page\APageAbstract implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/product/style.css';

        return $list;
    }

}
