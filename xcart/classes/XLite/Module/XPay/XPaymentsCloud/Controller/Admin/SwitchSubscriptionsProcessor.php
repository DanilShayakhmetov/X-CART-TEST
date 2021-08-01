<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Admin;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

class SwitchSubscriptionsProcessor extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @inheritDoc
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['switch']);
    }

    /**
     * Switch subscriptions processor
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionSwitch()
    {
        if (XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()) {
            $value = 0;
            $message = self::t('Subscriptions processor has been switched to X-Payments Subscriptions successfully');
        } else {
            $value = 1;
            $message = self::t('Subscriptions processor has been switched to X-Payments Cloud successfully');
        }

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'XPay\XPaymentsCloud',
            'name'     => 'use_xp_cloud_for_subscriptions',
            'value'    => $value,
        ]);

        \XLite\Core\TopMessage::addInfo($message);
        \XLite\Core\Database::getEM()->flush();
        $this->printAJAX([]);

        // If we do not call die() after parent printAJAX method, top messages are not displayed. So we do it here
        die();
    }

}
