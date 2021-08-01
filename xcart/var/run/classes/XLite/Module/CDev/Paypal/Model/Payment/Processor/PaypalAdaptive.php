<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

use Includes\Utils\Module\Manager;
use XLite\Model\Payment\BackendTransaction;
use XLite\Module\CDev\Paypal;

/**
 * Paypal Adaptive payment processor
 */
class PaypalAdaptive extends \XLite\Model\Payment\Base\WebBased
{
    /**
     * Referral page URL 
     * 
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/merchant';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'https://developer.paypal.com/docs/classic/lifecycle/goingLive/#credentials';

    /**
     * Partner code
     *
     * @var string
     */
    protected static $partnerCode = 'XCart_AP';

    /**
     * Get URL of referral page
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getPartnerPageURL(\XLite\Model\Payment\Method $method)
    {
        return \XLite::getXCartURL('https://www.x-cart.com/paypal_shopping_cart.html');
    }

    /**
     * Get knowledge base page URL
     *
     * @return string
     */
    public function getKnowledgeBasePageURL()
    {
        return $this->knowledgeBasePageURL;
    }

    /**
     * Get knowledge base page URL
     *
     * @return array
     */
    public function getKnowledgeBasePageURLs()
    {
        return array(
            array(
                'name'  =>  static::t('Obtaining your live PayPal credentials'),
                'url'   =>  'https://developer.paypal.com/docs/classic/lifecycle/goingLive/#credentials',
            ),
            array(
                'name'  =>  static::t('Registering your application with PayPal'),
                'url'   =>  'https://developer.paypal.com/docs/classic/lifecycle/goingLive/#register',
            ),
        );
    }

    /**
     * Get URL of referral page
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getReferralPageURL(\XLite\Model\Payment\Method $method)
    {
        return $this->referralPageURL;
    }

    /**
     * Get URL of help page
     *     *
     * @return string
     */
    public function getHelpFeesPageURL()
    {
        return 'https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/#id091QF0N0MPF__id092SH0050HS';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->api = new Paypal\Core\PaypalAdaptiveAPI();

        $method = Paypal\Main::getPaymentMethod(
            Paypal\Main::PP_METHOD_PAD
        );

        $this->api->setMethod($method);
        $this->api->setPartnerCode(static::$partnerCode);
    }

    /**
     * Payment method has settings into Module settings section
     *
     * @return boolean
     */
    public function hasModuleSettings()
    {
        return true;
    }

    /**
     * Return false to use own submit button on payment method settings form
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return false;
    }

    /**
     * Get allowed backend transactions
     *
     * @return string[] Statuses
     */
    public function getAllowedTransactions()
    {
        return [
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
//            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
//            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI
        ];
    }

    /**
     * Get payment method configuration page URL
     *
     * @param \XLite\Model\Payment\Method $method    Payment method
     * @param boolean                     $justAdded Flag if the method is just added via administration panel.
     *                                               Additional init configuration can be provided OPTIONAL
     *
     * @return string
     */
    public function getConfigurationURL(\XLite\Model\Payment\Method $method, $justAdded = false)
    {
        return \XLite\Core\Converter::buildURL('paypal_settings', '', array('method_id' => $method->getMethodId()));
    }

    /**
     * Get payment method row checkout template
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getCheckoutTemplate(\XLite\Model\Payment\Method $method)
    {
        return 'modules/CDev/Paypal/checkout/paypal.twig';
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return $this->api->isTestMode()
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Get form method
     *
     * @return string
     */
    protected function getFormMethod()
    {
        return static::FORM_METHOD_GET;
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     * @see    https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables
     */
    protected function getFormFields()
    {
        $params = array(
            'cmd' => '_ap-payment',
        );

        $paypalAdaptiveResponse = $this->api->doPayCall(
            $this->getOrder(),
            $this->getReturnURL(null, true, true),  // Cancel
            $this->getReturnURL(null, true),        // Return
            $this->getCallbackURL(null, true)       // IPN Notification URL
        );

        if (isset($paypalAdaptiveResponse['payKey'])) {
            $params['paykey'] = $paypalAdaptiveResponse['payKey'];

            $this->setDetail('used_paykey', $params['paykey']);

            $setPaymentOptionsResponse = $this->api->doSetPaymentOptionsCall(
                $params['paykey']
            );
        }

        return $params;
    }


    /**
     * Get transaction detail record
     *
     * @param string                                  $name               Code
     * @param \XLite\Model\Payment\BackendTransaction $backendTransaction Backend transaction object OPTIONAL
     *
     * @return mixed
     */
    protected function getDetail($name, $backendTransaction = null)
    {
        $transaction = isset($backendTransaction) ? $backendTransaction : $this->transaction;

        $cell = $transaction->getDataCell($name);
        return $cell
            ? $cell->getValue()
            : null;
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        return array(
            'status'         => 'Status',
            'fees_payer'     => 'Fees payer',
            'sender_email'   => 'Customer\'s primary email address',
            'txnId'          => 'Original transaction identification number',
            'reason_code'    => 'Reason code',
            'trackingId'     => 'Tracking ID',
        );
    }

    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

        if (\XLite\Core\Request::getInstance()->cancel) {
            if ($this->api->isTransactionCancellable($transaction)) {
                $this->setDetail(
                    'cancel',
                    'Customer has canceled checkout before completing their payments'
                );
                $this->transaction->setStatus($transaction::STATUS_CANCELED);
            }

        } elseif ($transaction::STATUS_INPROGRESS == $this->transaction->getStatus()) {
            $this->transaction->setStatus($transaction::STATUS_PENDING);
        }
    }

    /**
     * Update status of backend transaction related to an initial payment transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     * @param string                           $status      Transaction status
     *
     * @return void
     */
    public function updateInitialBackendTransaction(\XLite\Model\Payment\Transaction $transaction, $status)
    {
        $backendTransaction = $transaction->getInitialBackendTransaction();

        if (null !== $backendTransaction) {
            $backendTransaction->setStatus($status);
            $this->saveDataFromRequest($backendTransaction);
        }
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallback($transaction);

        if (Paypal\Model\Payment\Processor\PaypalIPN::getInstance()->isCallbackAdaptiveIPN()) {
            $result = Paypal\Model\Payment\Processor\PaypalIPN::getInstance()
                ->tryProcessCallbackIPN($transaction, $this);

            if ($result) {
                $transaction->getOrder()->setPaymentStatusByTransaction($transaction);
                \XLite\Core\Database::getEM()->flush();
            }
        }

        $this->saveDataFromRequest();
    }

    /**
     * @inheritdoc
     */
    public function processCallbackNotReady(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallbackNotReady($transaction);

        header('HTTP/1.1 409 Conflict', true, 409);
        header('Status: 409 Conflict');
        header('X-Robots-Tag: noindex, nofollow');
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     * @param boolean                                 $isDoVoid    Is void action OPTIONAL
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction, $isDoVoid = false)
    {
        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            $payKey = $this->getDetail('used_paykey');

            if ($payKey) {
                $result = $this->doInternalRefund($transaction, $payKey);

                if ($result) {
                    $backendTransactionStatus = $result;
                }

            } else {
                Paypal\Main::addLog(
                    'Adaptive payments error',
                    'There is no PAYKEY for refund'
                );
            }

        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            \XLite\Logger::getInstance()->log($e->getMessage(), LOG_ERR);
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);

        return \XLite\Model\Payment\BackendTransaction::STATUS_FAILED !== $backendTransactionStatus;

    }

    /**
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     * @param                                         $payKey
     *
     * @return bool
     */
    protected function doInternalRefund(\XLite\Model\Payment\BackendTransaction $transaction, $payKey)
    {
        return $this->doFullRefund($payKey);
    }

    /**
     * @param $payKey
     *
     * @return bool
     */
    protected function doFullRefund($payKey)
    {
        $result = $this->api->doFullRefundCall(
            $this->getOrder(),
            $payKey
        );

        $fullyRefunded = false;

        if ($result) {

            if (isset($result['refundInfoList']['refundInfo'])
                && isset($result['responseEnvelope']['ack'])
                && $result['responseEnvelope']['ack'] === 'Success'
                && is_array($result['refundInfoList']['refundInfo'])
            ) {
                $fullyRefunded = true;

                foreach ($result['refundInfoList']['refundInfo'] as $infoBlock) {
                    if ($infoBlock['refundStatus'] !== 'REFUNDED'
                        || $infoBlock['refundHasBecomeFull'] !== 'true'
                    ) {
                        $fullyRefunded = false;
                    }
                }

            }

        } else {
            Paypal\Main::addLog(
                'Adaptive payments error',
                'Refund was failed'
            );
        }

        return $fullyRefunded
            ? BackendTransaction::STATUS_PENDING
            : BackendTransaction::STATUS_FAILED;
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
            && $this->api->isConfigured()
            && Paypal\Main::PP_METHOD_PAD == $method->getServiceName()
            && Manager::getRegistry()->isModuleEnabled('XC', 'MultiVendor');
    }

    /**
     * Prevent enabling Paypal Adaptive if Multivendor is not installed and enabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function canEnable(\XLite\Model\Payment\Method $method)
    {
        return parent::canEnable($method)
            && Paypal\Main::PP_METHOD_PAD == $method->getServiceName()
            && Manager::getRegistry()->isModuleEnabled('XC', 'MultiVendor');
    }

    /**
     * Get warning note by payment method
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getWarningNote(\XLite\Model\Payment\Method $method)
    {
        $result = parent::getWarningNote($method);

        if (Paypal\Main::PP_METHOD_PAD === $method->getServiceName()
            && !Manager::getRegistry()->isModuleEnabled('XC', 'MultiVendor')
        ) {
            $result = static::t('To enable this payment method, you need Multi-vendor module installed.');
        }

        return $result;
    }

    /**
     * Multivendor must be enabled
     *
     * @param \XLite\Model\Order          $order  Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        return $this->canEnable($method) && parent::isApplicable($order, $method);
    }
}
