<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Admin;

use \XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * X-Payments Cloud connector
 *
 */
class PaymentMethod extends \XLite\Controller\Admin\PaymentMethod implements \XLite\Base\IDecorator
{

    /**
     * Checks if just_added flag is set
     *
     * @return bool
     */
    public function getXpaymentsJustAdded()
    {
        return (bool)\XLite\Core\Request::getInstance()->just_added;
    }

    /**
     * Register shop URL for empty account
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        $account = $this->getPaymentMethod()->getSetting('account');
        $checkAccount = (empty($account) || 'localhost' == $account);

        if (
            $this->isXpaymentsOperatedMethod()
            && $checkAccount
            && \XLite::getInstance()->getOptions(array('service', 'is_cloud'))
        ) {
            XPaymentsCloud::registerCloudShopUrl();
        }
    }

    /**
     * Returns X-Payments Cloud main payment method instance
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getXpaymentsPaymentMethod()
    {
        return XPaymentsCloud::getPaymentMethod();
    }

    /**
     * Checks if current payment method is Apple Pay
     *
     * @return bool
     */
    public function isXpaymentsApplePay()
    {
        return $this->getPaymentMethod()->isXpaymentsApplePay();
    }

    /**
     * Checks if current payment method is X-Payments Cloud or Apple Pay
     *
     * @return bool
     */
    public function isXpaymentsOperatedMethod()
    {
        return $this->getPaymentMethod()->isXpayments();
    }

    /**
     * Save connect settings
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionUpdate()
    {
        if ($this->isXpaymentsOperatedMethod()) {
            $wasConfigured = $this->getPaymentMethod()->isConfigured();
            if (!$wasConfigured) {
                // Set fake flag to trigger auto-enable of XP Cloud when it is configured inside parent method
                \XLite\Core\Request::getInstance()->just_added = true;
            }
        }

        parent::doActionUpdate();

        if ($this->isXpaymentsOperatedMethod()) {
            // Actually here will be only main XP Cloud method because Apple Pay submits settings to main method only
            $this->setSilenceClose(true);
            \XLite\Core\TopMessage::getInstance()->clearAJAX();

            $updatedMethod = $this->getPaymentMethod();

            if ($wasConfigured != $updatedMethod->isConfigured()) {
                if (!$wasConfigured && $updatedMethod->isEnabled()) {
                    // Automatically enable Apple Pay if main method was configured and enabled
                    XPaymentsCloud::getApplePayMethod()->setEnabled(true);
                    \XLite\Core\Database::getEM()->flush();
                }
                \XLite\Core\Event::xpaymentsReloadPaymentStatus();
            }

        }
    }

}
