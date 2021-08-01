<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core;

/**
 * PayflowAPI
 */
class PayflowAPI extends \XLite\Module\CDev\Paypal\Core\AAPI
{
    /**
     * Request length limit
     */
    const REQUEST_LENGTH_LIMIT = 2048;

    /**
     * Cache of SecureTokenID
     *
     * @var string
     */
    protected $secureTokenId = null;

    /**
     * Partner code
     *
     * @var string
     */
    protected $partnerCode = 'XCART5_Cart';

    // {{{ Common

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    // }}}

    // {{{ Configuration

    /**
     * Check - payment method is configured or not
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->getSetting('vendor')
            && $this->getSetting('pwd');
    }

    // }}}

    // {{{ Helpers

    /**
     * Get payment action
     * Auth available only for API Credentials
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return 'A' === $this->getSetting('transaction_type')
            ? 'A'
            : 'S';
    }

    /**
     * Get SecureTokenId
     *
     * @return string
     */
    public function getSecureTokenId()
    {
        if (!isset($this->secureTokenId)) {
            $this->secureTokenId = $this->generateSecureTokenId();
        }

        return $this->secureTokenId;
    }

    /**
     * Returns merchant id
     *
     * @return string
     */
    public function getMerchantID()
    {
        return '';
    }

    /**
     * Generate random string for SecureTokenId
     *
     * @return string
     */
    protected function generateSecureTokenId()
    {
        return md5(time() + mt_rand(1000, 99999));
    }

    /**
     * Returns order items
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return mixed
     */
    protected function getItems($order)
    {
        $result        = [];
        $itemsSubtotal = 0;

        if ($order->countItems()) {
            $index = 0;

            /** @var \XLite\Model\Currency $currency */
            $currency = $order->getCurrency();

            foreach ($order->getItems() as $item) {
                $amount                    = $currency->roundValue($item->getItemNetPrice());
                $result['L_COST' . $index] = $amount;

                /** @var \XLite\Model\Product $product */
                $product                   = $item->getProduct();
                $result['L_NAME' . $index] = $product->getName();

                if ($item->getSku()) {
                    $result['L_SKU' . $index] = $item->getSku();
                }

                $attributesValues = [];
                foreach ($item->getAttributeValues() as $k => $value) {
                    $attrValue = $value->getValue();
                    if ($value) {
                        $attributesValues[] = $value->getActualName() . ': ' . $attrValue;
                    }
                }

                if ($attributesValues) {
                    $result['L_DESC' . $index] = implode(', ', $attributesValues);
                }

                $qty                      = $item->getAmount();
                $result['L_QTY' . $index] = $qty;
                $itemsSubtotal            += $amount * $qty;
                $index                    += 1;
            }

            // Prepare data about discount
            $discount = $currency->roundValue(
                $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT)
            );

            if (0 != $discount) {
                $result['L_NAME' . $index] = 'discount';
                $result['L_COST' . $index] = $discount;

                $itemsSubtotal += $discount;

                // ITEMAMT must be greater than zero (min value is 0.01)
                if ($itemsSubtotal <= 0) {
                    $minValue                  = 0.01;
                    $newItemsDiscount          = $discount - $itemsSubtotal + $minValue;
                    $result['L_COST' . $index] = $currency->roundValue($newItemsDiscount);
                    $itemsSubtotal             = $minValue;

                    $result['DISCOUNT'] = $currency->roundValue($newItemsDiscount - $discount);
                }
            }

            $result += ['ITEMAMT' => $itemsSubtotal];

            // Prepare data about summary tax cost

            $taxCost = $currency->roundValue(
                $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX)
            );

            if (0 < $taxCost) {
                $result['L_TAXAMT' . $index] = $taxCost;
                $result['TAXAMT']            = $taxCost;
            }
        }

        return $result;
    }

    // }}}

    // {{{ GetPalDetails

    /**
     * Do GetPalDetails
     *
     * @return mixed
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function doGetPalDetails()
    {
        $params = [

        ];

        return $this->doRequest('GetPalDetails');
    }

    // }}}

    // {{{ SetExpressCheckout

    /**
     * Convert order to array for SetExpressCheckout
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function convertSetExpressCheckoutParams($order)
    {
        /** @var \XLite\Model\Currency $currency */
        $currency = $order->getCurrency();

        $orderTotal = $currency->roundValue($order->getTotal());

        $shippingCost = $this->getShippingCost($order);

        /** @var \XLite\Module\CDev\Paypal\Model\Payment\Processor\APaypal $processor */
        $processor = $this->getProcessor();

        $params = [
            'TRXTYPE'      => $this->getPaymentAction(),
            'TENDER'       => 'P',
            'ACTION'       => 'S',
            'RETURNURL'    => $processor->getPaymentReturnUrl(),
            'CANCELURL'    => $processor->getPaymentCancelUrl(),
            'AMT'          => $orderTotal,
            'CURRENCY'     => $currency->getCode(),
            'FREIGHTAMT'   => (float) $shippingCost,
            'HANDLINGAMT'  => 0,
            'INSURANCEAMT' => 0,
            'NOSHIPPING'   => null === $shippingCost ? '1' : '0',
            'ALLOWNOTE'    => 1,
            'LOCALECODE'   => strtoupper(\XLite\Core\Session::getInstance()->getLanguage()->getCode()),
        ];

        if (\XLite\Core\Config::getInstance()->Security->customer_security) {
            $params['HDRIMG'] = urldecode(\XLite\Module\CDev\Paypal\Main::getLogo());
        }

        $items = $this->getItems($order);

        // To avoid total mismatch clear tax, shipping discount and shipping cost
        $taxAmt              = $items['TAXAMT'] ?? 0;
        $shippingDiscountAmt = $items['DISCOUNT'] ?? 0;

        if (abs($orderTotal - $items['ITEMAMT'] - $taxAmt - $shippingCost - $shippingDiscountAmt) <= 0.0000000001) {
            $params += $items;

        } else {
            $params['ITEMAMT']    = $orderTotal;
            $params['DISCOUNT']   = 0;
            $params['TAXAMT']     = 0;
            $params['FREIGHTAMT'] = 0;
        }

        $type = \XLite\Core\Session::getInstance()->ec_type;

        /** @var \XLite\Model\Profile $profile */
        $profile = $order->getProfile();

        if (\XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout::EC_TYPE_SHORTCUT == $type) {
            $params['REQCONFIRMSHIPPING'] = 0;
        }

        if ($profile && $profile->getLogin()) {
            $params += [
                'EMAIL' => $profile->getLogin(),
            ];
        }

        if ($profile && $profile->getBillingAddress()) {
            $params += [
                'PHONENUM' => $profile->getBillingAddress()->getPhone(),
            ];
        }

        if (null !== $shippingCost
            && $profile
            && $profile->getShippingAddress()
            && $profile->getShippingAddress()->isCompleted(\XLite\Model\Address::SHIPPING)
        ) {
            /** @var \XLite\Model\Address $address */
            $address = $profile->getShippingAddress();

            $params += ['ADDROVERRIDE' => 1];
            $params += $this->getConfirmedShippingAddress($address);
        }

        return $params;
    }

    /**
     * @param string $language
     *
     * @return string
     */
    protected function getLocaleCode($language)
    {
        $locales = [
            'zh' => 'CN',
            'de' => 'DE',
            'es' => 'ES',
            'fr' => 'FR',
            'it' => 'IT',
            'jp' => 'JP',
            'nl' => 'NL',
            'pl' => 'PL',
            'en' => 'US',
        ];

        return isset($locales[$language]) ? $locales[$language] : 'US';
    }

    // }}}

    // {{{ GetExpressCheckoutDetails

    /**
     * Get confirmed on our side address
     *
     * @param \XLite\Model\Address $address Address model
     *
     * @return array
     */
    protected function getConfirmedShippingAddress(\XLite\Model\Address $address)
    {
        $countryCode = $address->getCountry()
            ? $address->getCountry()->getCode()
            : '';

        $stateCode = $address->getState()
            ? ($address->getState()->getCode() ?: $address->getState()->getState())
            : '';

        return [
            'SHIPTONAME'    => trim($address->getFirstname() . ' ' . $address->getLastname()),
            'SHIPTOSTREET'  => $address->getStreet(),
            'SHIPTOSTREET2' => '',
            'SHIPTOCITY'    => $address->getCity(),
            'SHIPTOSTATE'   => $stateCode,
            'SHIPTOZIP'     => $address->getZipcode(),
            'SHIPTOCOUNTRY' => $countryCode,
        ];
    }

    /**
     * Convert order to array for GetExpressCheckoutDetails
     *
     * @param string $token Token
     *
     * @return array
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function convertGetExpressCheckoutDetailsParams($token)
    {
        return [
            'TRXTYPE' => $this->getPaymentAction(),
            'TENDER'  => 'P',
            'ACTION'  => 'G',
            'TOKEN'   => $token,
        ];
    }

    // }}}

    // {{{ DoExpressCheckoutPayment

    /**
     * Convert order to array for DoExpressCheckoutPayment
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param string                           $token       Token
     * @param string                           $payerId     Payer id
     *
     * @return array
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function convertDoExpressCheckoutPaymentParams($transaction, $token, $payerId)
    {
        /** @var \XLite\Model\Order $order */
        $order = $transaction->getOrder();

        /** @var \XLite\Model\Currency $currency */
        $currency = $order->getCurrency();

        $orderTotal = $currency->roundValue($transaction->getValue());

        $shippingCost = $this->getShippingCost($order);

        /** @var \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPI $processor */
        $processor = $this->getProcessor();

        $params = [
            'TRXTYPE'      => $this->getPaymentAction(),
            'TENDER'       => 'P',
            'ACTION'       => 'D',
            'TOKEN'        => $token,
            'PAYERID'      => $payerId,
            'AMT'          => $orderTotal,
            'CURRENCY'     => $currency->getCode(),
            'FREIGHTAMT'   => (float) $shippingCost,
            'HANDLINGAMT'  => 0,
            'INSURANCEAMT' => 0,
            'NOTIFYURL'    => $processor->getPaymentCallbackUrl(),
            'ALLOWNOTE'    => 1,
        ];

        /** @var \XLite\Model\Profile $profile */
        $profile = $order->getProfile();

        if ($profile
            && $profile->getShippingAddress()
            && $profile->getShippingAddress()->isCompleted(\XLite\Model\Address::SHIPPING)
        ) {
            $params += $this->getConfirmedShippingAddress(
                $profile->getShippingAddress()
            );
        }

        $items = $this->getItems($order);

        // To avoid total mismatch clear tax and shipping cost
        $taxAmt              = $items['TAXAMT'] ?? 0;
        $shippingDiscountAmt = $items['DISCOUNT'] ?? 0;

        if (abs($orderTotal - $items['ITEMAMT'] - $taxAmt - $shippingCost - $shippingDiscountAmt) > 0.0000000001) {
            $items['ITEMAMT']     = $orderTotal;
            $items['TAXAMT']      = 0;
            $params['FREIGHTAMT'] = 0;
            $params['DISCOUNT']   = 0;
        }

        $params += $items;

        return $params;
    }

    // }}}

    // {{{ CreateSecureToken

    /**
     * Convert order to array for CreateSecureToken
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function convertCreateSecureTokenParams($order)
    {
        /** @var \XLite\Model\Currency $currency */
        $currency = $order->getCurrency();

        $orderTotal = $currency->roundValue($order->getTotal());

        $shippingCost = $this->getShippingCost($order);

        /** @var \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPI $processor */
        $processor = $this->getProcessor();

        /** @var \XLite\Model\Profile $profile */
        $profile = $order->getProfile();

        /** @var \XLite\Model\Address $billingAddress */
        $billingAddress = $profile->getBillingAddress();

        $postData = [
            'CREATESECURETOKEN' => 'Y',
            'SECURETOKENID'     => $this->getSecureTokenId(),
            'TRXTYPE'           => $this->getPaymentAction(),
            'AMT'               => $orderTotal,
            'BILLTOFIRSTNAME'   => $billingAddress->getFirstname(),
            'BILLTOLASTNAME'    => $billingAddress->getLastname(),
            'BILLTOSTREET'      => $billingAddress->getStreet(),
            'BILLTOCITY'        => $billingAddress->getCity(),
            'BILLTOSTATE'       => $billingAddress->getState()->getCode() ?: $billingAddress->getState()->getState(),
            'BILLTOZIP'         => $billingAddress->getZipcode(),
            'BILLTOCOUNTRY'     => strtoupper($billingAddress->getCountry()->getCode()),
            'ERRORURL'          => $processor->getPaymentReturnUrl(),
            'RETURNURL'         => $processor->getPaymentReturnUrl(),
            'CANCELURL'         => $processor->getPaymentCancelUrl(),
            'NOTIFYURL'         => $processor->getPaymentCallbackUrl(),
            'RETURNURLMETHOD'   => 'POST', // Set the return method for approved transactions (RETURNURL)
            'URLMETHOD'         => 'POST', // Set the return method for cancelled and failed transactions (ERRORURL, CANCELURL)
            'TEMPLATE'          => 'MINLAYOUT', // This enables an iframe layout
            'BILLTOPHONENUM'    => $billingAddress->getPhone(),
            'BILLTOEMAIL'       => $profile->getLogin(),
            'ADDROVERRIDE'      => '1',
            'NOSHIPPING'        => null === $shippingCost ? '1' : '0',
            'FREIGHTAMT'        => (float) $shippingCost,
            'HANDLINGAMT'       => 0,
            'INSURANCEAMT'      => 0,
            'SILENTPOST'        => 'TRUE',
            'SILENTPOSTURL'     => $processor->getPaymentCallbackUrl(),
            'FORCESILENTPOST'   => 'FALSE',
            'DISABLERECEIPT'    => 'TRUE', // Warning! If set this to 'FALSE' Paypal will redirect buyer to cart.php without target, txnId and other service parameters
            'CURRENCY'          => $currency->getCode(),
        ];

        if (null !== $shippingCost) {
            /** @var \XLite\Model\Address $shippingAddress */
            $shippingAddress = $profile->getShippingAddress();

            $postData += [
                'SHIPTOPHONENUM'  => $shippingAddress->getPhone(),
                'SHIPTOFIRSTNAME' => $shippingAddress->getFirstname(),
                'SHIPTOLASTNAME'  => $shippingAddress->getLastname(),
                'SHIPTOSTREET'    => $shippingAddress->getStreet(),
                'SHIPTOCITY'      => $shippingAddress->getCity(),
                'SHIPTOSTATE'     => $shippingAddress->getState()->getCode() ?: $shippingAddress->getState()->getState(),
                'SHIPTOZIP'       => $shippingAddress->getZipcode(),
                'SHIPTOCOUNTRY'   => $shippingAddress->getCountry()->getCode(),
                'SHIPTOEMAIL'     => $profile->getLogin(),
            ];
        }

        $items = $this->getItems($order);

        // To avoid total mismatch clear tax and shipping cost
        $taxAmt              = $items['TAXAMT'] ?? 0;
        $shippingDiscountAmt = $items['DISCOUNT'] ?? 0;

        if (abs($orderTotal - $items['ITEMAMT'] - $taxAmt - $shippingCost + $shippingDiscountAmt) > 0.0000000001) {
            $items['ITEMAMT']       = $orderTotal;
            $items['TAXAMT']        = 0;
            $items['DISCOUNT']      = 0;
            $postData['FREIGHTAMT'] = 0;
        }

        if (static::REQUEST_LENGTH_LIMIT > strlen($this->convertParams($postData) . $this->convertParams($items))) {
            $postData += $items;
        }

        return $postData;
    }

    // }}}

    // {{{ Capture

    /**
     * Convert order to array for Capture
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction   Transaction
     * @param string                                  $transactionId Transaction id
     *
     * @return array
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function convertCaptureParams($transaction, $transactionId)
    {
        return [
            'TRXTYPE'         => 'D',
            'ORIGID'          => $transactionId,
            'AMT'             => $this->getCaptureAmount($transaction),
            'CAPTURECOMPLETE' => 'Y', // For Paypal Payments Advanced only (todo: remove after implementing partial amount transactions)
        ];
    }

    // }}}

    // {{{ Void

    /**
     * Convert order to array for Void
     *
     * @param string $transactionId Transaction id
     *
     * @return array
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function convertVoidParams($transactionId)
    {
        return [
            'TRXTYPE' => 'V',
            'ORIGID'  => $transactionId,
        ];
    }

    // }}}

    // {{{ Credit

    /**
     * Convert order to array for Credit
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction   Transaction
     * @param string                                  $transactionId Transaction id
     *
     * @return array
     * @see    https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     */
    public function convertCreditParams($transaction, $transactionId)
    {
        /** @var \XLite\Model\Order $order */
        $order = $transaction->getPaymentTransaction()->getOrder();

        /** @var \XLite\Model\Currency $currency */
        $currency = $order->getCurrency();

        $amount = $currency->roundValue($transaction->getValue());

        return [
            'TRXTYPE' => 'C',
            'ORIGID'  => $transactionId,
            'AMT'     => $amount,
        ];
    }

    // }}}

    // {{{ Backend request

    /**
     * Convert request params from array to string
     *
     * @param array $params Params
     *
     * @return string
     */
    protected function convertParams($params)
    {
        $data = [];

        foreach ($params as $k => $v) {
            $data[] = sprintf('%s[%d]=%s', $k, strlen($v), $v);
        }

        $data = implode('&', $data);

        return $data;
    }

    /**
     * Returns common request params required for all requests
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $result = [
            'VENDOR'       => $this->getSetting('vendor'),
            'USER'         => $this->getSetting('user') ?: $this->getSetting('vendor'),
            'PWD'          => $this->getSetting('pwd'),
            'PARTNER'      => $this->getSetting('partner') ?: 'PayPal',
            'BUTTONSOURCE' => $this->partnerCode,
            'VERBOSITY'    => 'HIGH',
        ];

        // todo: remove?!
        if (\XLite\Core\Config::getInstance()->Security->customer_security) {
            $result['HDRIMG'] = \XLite\Module\CDev\Paypal\Main::getLogo();
        }

        return $result;
    }

    /**
     * Prepare url
     *
     * @param string $url    Url
     * @param string $type   Request type
     * @param array  $params Request params
     *
     * @return string
     */
    protected function prepareUrl($url, $type, $params)
    {
        $url = $this->isTestMode()
            ? 'https://pilot-payflowpro.paypal.com/'
            : 'https://payflowpro.paypal.com/';

        return parent::prepareUrl($url, $type, $params);
    }

    // }}}
}
