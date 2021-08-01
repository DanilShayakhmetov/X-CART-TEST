<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\FormModel\Settings\Notification;

use \XLite\Core\Mailer;

class Notification extends \XLite\View\FormModel\Settings\Notification\Notification implements \XLite\Base\IDecorator
{
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $buttonsToExclude = ['preview', 'send_test_email'];

        $templatesDirectory = \XLite\Core\Request::getInstance()->getData()['templatesDirectory'];

        if (false !== strpos($templatesDirectory, Mailer::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX)) {
            foreach ($result as $key => $value) {
                if (in_array($key, $buttonsToExclude)) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }

}
