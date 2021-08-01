<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Order\Details\Admin;

use XLite\Module\XPay\XPaymentsCloud\Model\Payment\XpaymentsFraudCheckData as FraudCheckData;
use XLite\Model\Order;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;

/**
 * Order info
 */
 class Info extends \XLite\View\Order\Details\Admin\InfoAbstract implements \XLite\Base\IDecorator
{
    /**
     * Get skin directory
     *
     * @return string
     */
    protected static function getDirectory()
    {
        return 'modules/XPay/XPaymentsCloud/order';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = self::getDirectory() . '/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = self::getDirectory() . '/script.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list['css'][] = 'modules/XPay/XPaymentsCloud/account/cc_type_sprites.css';

        return $list;
    }

    /**
     * Check - display AntiFraud module advertisement or not
     *
     * @return boolean
     */
    protected function isDisplayAntiFraudAd()
    {
        $result = parent::isDisplayAntiFraudAd();

        if ($result) {

            if ($this->getOrder()->getXpaymentsFraudCheckData()) {

                foreach ($this->getOrder()->getXpaymentsFraudCheckData() as $fraudCheckData) {
                    if (FraudCheckData::CODE_ANTIFRAUD == $fraudCheckData->getCode()) {
                        $result = false;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getFraudStatusText()
    {
        $text = '';

        $fraudCheckData = $this->getOrder()->getXpaymentsFraudCheckData();

        if ($fraudCheckData) {
            foreach ($fraudCheckData as $item) {
                $result = $item->getResult();
                break;
            }
        }

        if (FraudCheckData::RESULT_MANUAL == $result) {
            $text = static::t('Payment transaction is potentially fraudulent.');
        } elseif (FraudCheckData::RESULT_FAIL == $result) {
            $text = static::t('Payment transaction was fraudulent.');
        } elseif (FraudCheckData::RESULT_PENDING == $result) {
            $text = static::t('Transaction is being reviewed for fraud.');
        };

        return $text;
    }

    /**
     * @return Order
     */
    protected function getOrder()
    {
        return \XLite::getController()->getOrder();
    }

    /**
     * Is next payment date available for current order
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsNextPaymentDateAvailable($item)
    {
        return $item->isXpaymentsNextPaymentDateAvailable();
    }

    /**
     * Is last payment failed for current subscription
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentFailed($item)
    {
        $subscription = $item->getXpaymentsSubscription();

        return $subscription
            && $subscription->getActualDate() > $subscription->getPlannedDate();
    }

    /**
     * Get next payment date
     *
     * @param Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextPaymentDate($subscription)
    {
        return $subscription->getPlannedDate();
    }

    /**
     * Get next attempt date
     *
     * @param Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextAattemptDate($subscription)
    {
        return $subscription->getActualDate();
    }

     /* Is recharge allowed for the orde
     *
     * @return bool
     */
    protected function isXpaymentsChargeDifferenceAvailable()
    {
        return $this->getOrder()->isXpaymentsChargeDifferenceAvailable();
    }

    /**
     * Javascript code for recharge popup
     *
     * @return string
     */
    protected function getXpaymentsChargeDifferenceJsCode()
    {
        $orderNumber = '\'' . $this->getOrder()->getOrderNumber() . '\'';
        $amount = '\'' . $this->getOrder()->getAomTotalDifference() . '\'';

        $code = 'showRebillBox(' . $orderNumber . ', ' . $amount . ');';

        return $code;
    }

}
