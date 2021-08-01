<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

/**
 * 3-D Secure page for X-Payments Cloud
 *
 * @ListChild (list="center")
 */
class XPaymentsCloud extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'checkoutPayment';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'checkout/css/animations.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
           && $this->get3DSecureURL();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/checkout/secure3d.twig';
    }

    /**
     * Returns 3-D Secure url
     *
     * @return string
     */
    protected function get3DSecureURL()
    {
        $mode = \XLite\Core\Request::getInstance()->mode;
        if ('CardSetup' == $mode) {
            $data = \XLite\Core\Session::getInstance()->xpaymentsCardSetupData;
        } else {
            $data = \XLite\Core\Session::getInstance()->xpaymentsData;
        }

        return $data && $data['redirectUrl']
            ? $data['redirectUrl']
            : null;
    }
}
