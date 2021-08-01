<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Form\Product\Modify;

/**
 * Details
 */
class Single extends \XLite\View\Form\Product\Modify\Single implements \XLite\Base\IDecorator
{
    /**
     * Get js files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/js/product_modify_single.js';

        return $list;
    }

}
