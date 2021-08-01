<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\Payment;

/**
 * List of payment methods for popup widget
 */
 class MethodsPopupList extends \XLite\View\Payment\MethodsPopupListAbstract implements \XLite\Base\IDecorator
{
    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BraintreeVZ/payment/methods_popup_list/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/BraintreeVZ/config/style.css';

        return $list;
    }	
}
