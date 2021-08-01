<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model;

use XLite\Model\Order\Status\Payment as PaymentStatus;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XLite\Module\XPay\XPaymentsCloud\Model\Payment\XpaymentsFraudCheckData as FraudCheckData;
use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;
use XLite\Module\XPay\XPaymentsCloud\Core\ApplePay as XPaymentsApplePay;
/**
 * X-Payments Specific order fields
 *
 * @Table (indexes={
 *      @Index (name="buyWithApplePay", columns={"buyWithApplePay"}),
 *  })
 *
 */
 class Order extends \XLite\Model\OrderAbstract implements \XLite\Base\IDecorator
{
    /**
     * Fraud statuses
     */
    const FRAUD_STATUS_CLEAN    = 'Clean';
    const FRAUD_STATUS_FRAUD    = 'Fraud';
    const FRAUD_STATUS_REVIEW   = 'Review';
    const FRAUD_STATUS_ERROR    = 'Error';
    const FRAUD_STATUS_UNKNOWN  = '';

    /**
     * Order fraud status
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $xpaymentsFraudStatus = '';

    /**
     * Order fraud type (which system considered the transaction fraudulent)
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $xpaymentsFraudType = '';

    /**
     * Transaction with fraud check data
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $xpaymentsFraudCheckTransactionId = 0;

    /**
     * Flag to mark carts created for Buy With Apple Pay feature
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $buyWithApplePay = false;

    /**
     * Fraud check data from transaction
     */
    protected $xpaymentsFraudCheckData = false;

    /**
     * Hash for X-Payments cards
     */
    protected $xpaymentsCards = null;

    /**
     * @param bool $value
     *
     * @return Order
     */
    public function markAsBuyWithApplePay($value = true)
    {
        $this->buyWithApplePay = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBuyWithApplePay()
    {
        return $this->buyWithApplePay;
    }

    /**
     * @return string
     */
    public function getXpaymentsFraudStatus()
    {
        return $this->xpaymentsFraudStatus;
    }

    /**
     * @param string $xpaymentsFraudStatus
     *
     * @return Order
     */
    public function setXpaymentsFraudStatus($xpaymentsFraudStatus)
    {
        $this->xpaymentsFraudStatus = $xpaymentsFraudStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getXpaymentsFraudType()
    {
        return $this->xpaymentsFraudType;
    }

    /**
     * @param string $xpaymentsFraudType
     *
     * @return Order
     */
    public function setXpaymentsFraudType($xpaymentsFraudType)
    {
        $this->xpaymentsFraudType = $xpaymentsFraudType;
        return $this;
    }

    /**
     * @return int
     */
    public function getXpaymentsFraudCheckTransactionId()
    {
        return $this->xpaymentsFraudCheckTransactionId;
    }

    /**
     * @param int $xpaymentsFraudCheckTransactionId
     *
     * @return Order
     */
    public function setXpaymentsFraudCheckTransactionId($xpaymentsFraudCheckTransactionId)
    {
        $this->xpaymentsFraudCheckTransactionId = $xpaymentsFraudCheckTransactionId;
        return $this;
    }

    /**
     * Get fraud check data from transaction
     *
     * @return array
     */
    public function getXpaymentsFraudCheckData()
    {
        if (empty($this->xpaymentsFraudCheckData)) {

            if ($this->getXpaymentsFraudCheckTransactionId()) {

                $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->find(
                    $this->getXpaymentsFraudCheckTransactionId()
                );

                if ($transaction) {
                    $this->xpaymentsFraudCheckData = $transaction->getXpaymentsFraudCheckData();
                }
            }
        }

        return $this->xpaymentsFraudCheckData;
    }

    /**
     * Return anchor name for the information about fraud check on the order details page
     *
     * @return string
     */
    public function getXpaymentsFraudInfoAnchor()
    {
        return 'fraud-info-' . $this->getXpaymentsFraudType();
    }

    /**
     * Is order fraud
     *
     * @return bool
     */
    public function isFraudStatus()
    {
        $result = false;
        $fraudStatuses = [
            FraudCheckData::RESULT_MANUAL,
            FraudCheckData::RESULT_PENDING,
            FraudCheckData::RESULT_FAIL,
        ];
        $fraudCheckData = $this->getXpaymentsFraudCheckData();

        if ($fraudCheckData) {
            foreach ($fraudCheckData as $item) {
                if (in_array($item->getResult(), $fraudStatuses)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get X-Payments cards
     *
     * @return array
     */
    public function getXpaymentsCards()
    {
        if (null === $this->xpaymentsCards) {

            $transactions = $this->getPaymentTransactions();

            $this->xpaymentsCards = array();

            $adminUrl = \XLite\Module\XPay\XPaymentsCloud\Main::getClient()->getAdminUrl();

            foreach ($transactions as $transaction) {

                if (
                    !$transaction->isXpayments()
                    || !$transaction->getDataCell('xpaymentsCardNumber')
                    || !$transaction->getDataCell('xpaymentsCardType')
                    || !$transaction->getDataCell('xpaymentsCardExpirationDate')
                ) {
                    continue;
                }

                $card = array(
                    'cardNumber' => $transaction->getDataCell('xpaymentsCardNumber')->getValue(),
                    'cardType'   => $transaction->getDataCell('xpaymentsCardType')->getValue(),
                    'expire'     => $transaction->getDataCell('xpaymentsCardExpirationDate')->getValue(),
                    'xpid'       => $transaction->getXpaymentsId(),
                    'url'        => $adminUrl . '?target=payment&xpid=' . $transaction->getXpaymentsId(),
                );

                $card['cssType'] = strtolower($card['cardType']);

                $this->xpaymentsCards[] = $card;
            }
        }

        return $this->xpaymentsCards;
    }

    /**
     * Get calculated payment status
     *
     * @param boolean $override Override calculation cache OPTIONAL
     *
     * @return string
     */
    public function getCalculatedPaymentStatus($override = false)
    {
        $result = parent::getCalculatedPaymentStatus($override);

        if (PaymentStatus::STATUS_QUEUED == $result) {

            /** @var Transaction $lastTransaction */
            $lastTransaction = $this->getPaymentTransactions()->last();

            if ($lastTransaction->isXpayments()) {
                $backendTransactions = $lastTransaction->getBackendTransactions();
                if (
                    1 == count($backendTransactions)
                    && BackendTransaction::TRAN_TYPE_ACCEPT == $backendTransactions->last()->getType()
                ) {
                    $result = $lastTransaction->getOrder()->getPaymentStatus();
                }
            }
        }

        return $result;
    }

    /**
     * Process backordered items
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function processBackorderedItems()
    {
        if (
            $this->getItems()
            && $this->getItems()->last()
            && $this->getItems()->last()->isXpaymentsEmulated()
        ) {
            $result = 0;
        } else {
            $result = parent::processBackorderedItems();

        }

        return $result;
    }

    /**
     * Difference in order Total after AOM changes if (any)
     *
     * @return float
     */
    public function getAomTotalDifference()
    {
        return $this->getOpenTotal();
    }

    /**
     * Check if total difference after AOM changes is greater than zero
     *
     * @return bool
     */
    public function isAomTotalDifferencePositive()
    {
        return $this->getAomTotalDifference() > \XLite\Model\Order::ORDER_ZERO;
    }

    /**
     * Return array of active X-Payments saved cards of order customer's profile
     *
     * @return array
     */
    public function getActiveXpaymentsCards()
    {
        $cards = [];

        if (
            $this->getOrigProfile()
            && $this->getOrigProfile()->getXpaymentsCards()
        ) {
            $cards = $this->getOrigProfile()->getXpaymentsCards();
            foreach ($cards as $key => $value) {
                if (false === $cards[$key]['isActive']) {
                    unset($cards[$key]);
                }
            }
        }

        return $cards;
    }

    /**
     * Checks if at least one transaction is handled by X-Payments
     *
     * @return bool
     */
    protected function isXpayments()
    {
        $transactions = $this->getPaymentTransactions();

        $isXpayments = false;
        foreach ($transactions as $t) {
            if ($t->isXpayments()) {
                $isXpayments = true;
                break;
            }
        }

        return $isXpayments;
    }

    /**
     * Whether charge the difference is available for the order
     *
     * @return bool
     */
    public function isXpaymentsChargeDifferenceAvailable()
    {
        return $this->isXpayments()
            && $this->isAomTotalDifferencePositive()
            && !empty($this->getActiveXpaymentsCards());
    }

    /**
     * @return array
     */
    public function getPaymentTransactionSums()
    {
        $paymentTransactionSums = parent::getPaymentTransactionSums();

        if ($this->isXpaymentsChargeDifferenceAvailable()) {
            $difference = (string) static::t('Difference between total and paid amount');
            $paymentTransactionSums[$difference] = $this->getAomTotalDifference();
        }

        return $paymentTransactionSums;
    }

    /**
     * Exclude Apple Pay from the list of available for checkout payment methods
     * if browser can't support it at all
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $list = parent::getPaymentMethods();

        foreach ($list as $key => $method) {
            if ($method->isXpaymentsApplePay()) {
                if (!XPaymentsApplePay::isBrowserMaySupportApplePay()) {
                    unset($list[$key]);
                    $transaction = $this->getFirstOpenPaymentTransaction();
                    $paymentMethod = $transaction ? $transaction->getPaymentMethod() : null;
                    if (
                        $paymentMethod
                        && $paymentMethod->isXpaymentsApplePay()
                    ) {
                        $transaction->setPaymentMethod(null);
                    }
                }
                break;
            }
        }

        return $list;
    }

    // X-Payments subscriptions methods

    /**
     * Check if order has subscriptions
     *
     * @return boolean
     */
    public function hasXpaymentsSubscriptions()
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->isXpaymentsSubscription()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * isXpaymentsSubscriptionPayment
     *
     * @return boolean
     */
    public function isXpaymentsSubscriptionPayment()
    {
        $isXpaymentsSubscriptionPayment = false;

        foreach ($this->getItems() as $item) {
            if ($item->getXpaymentsSubscription()
                && !$item->isInitialXpaymentsSubscription()
            ) {
                $isXpaymentsSubscriptionPayment = true;
            }
        }

        return $isXpaymentsSubscriptionPayment;
    }

    /**
     * getXpaymentsSubscription
     *
     * @return Subscription
     */
    public function getXpaymentsSubscription()
    {
        $subscription = null;

        if ($this->isXpaymentsSubscriptionPayment()) {
            foreach ($this->getItems() as $item) {
                if ($item->isXpaymentsSubscription()
                    && !$item->isInitialXpaymentsSubscription()
                ) {
                    $subscription = $item->getXpaymentsSubscription();
                }
            }
        }

        return $subscription;
    }

    /**
     * Get shipping rates for order
     *
     * @return array
     */
    public function getShippingRates()
    {
        $rates = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING')->getRates();
        $result = [];
        foreach ($rates as $rate) {
            $result[] = [
                'method_id' => $rate->getMethodId(),
                'method_name' => $rate->getMethodName(),
                'total_rate' => $rate->getTotalRate(),
            ];
        }

        return $result;
    }

    /**
     * Get shipping address
     *
     * @return \XLite\Model\Address
     */
    public function getShippingAddress()
    {
        return $this->getProfile()->getShippingAddress();
    }

    // \ X-Payments subscriptions methods



}
