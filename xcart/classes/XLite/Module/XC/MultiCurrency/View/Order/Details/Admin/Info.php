<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\Order\Details\Admin;

/**
 * Class Info
 * @package XLite\Module\XC\MultiCurrency\View\Order\Details\Admin
 */
class Info extends \XLite\View\Order\Details\Admin\Info implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/MultiCurrency/shopping_cart/parts/aom_warning.less';

        return $list;
    }
}
