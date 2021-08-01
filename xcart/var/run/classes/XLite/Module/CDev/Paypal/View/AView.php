<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

use XLite\Module\CDev\Paypal;

/**
 * Abstarct widget
 */
abstract class AView extends \XLite\Module\CDev\Sale\View\AView implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        if (!\XLite::isAdminZone() && Paypal\Main::isPaypalCommercePlatformEnabled()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);

            if ($method->getProcessor()->isCurrencyApplicable($method)) {
                $list[] = 'modules/CDev/Paypal/button/paypal_commerce_platform/sdk.js';
            }
        }

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (!\XLite::isAdminZone()) {
            $list[] = 'modules/CDev/Paypal/style.css';
            $list[] = [
                'file'  => 'modules/CDev/Paypal/style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }
}
