<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

use XLite\Core\TopMessage;
use XLite\Module\CDev\Paypal;
use XLite\Module\CDev\Paypal\Model\Payment\Processor\PaypalCommercePlatform as PaypalCommercePlatformProcessor;

class PaypalCommercePlatform extends \XLite\Controller\Customer\Checkout
{
    /**
     * https://developer.paypal.com/docs/checkout/reference/server-integration/set-up-transaction/#on-the-server
     */
    public function doActionCreateOrder()
    {
        $paymentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);

        $this->getCart()->setPaymentMethod($paymentMethod);

        $transaction = $this->getCart()->getFirstOpenPaymentTransaction();

        if ($transaction) {
            /** @var PaypalCommercePlatformProcessor $processor */
            $processor = $paymentMethod->getProcessor();
            $result    = $processor->createOrder($transaction);

            \XLite\Core\Database::getEM()->flush();

            if (\XLite\Core\Request::getInstance()->hostedFields
                && (!$result || (is_array($result) && isset($result['message'])))
            ) {
                TopMessage::addError($result['message'] ?? 'Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.');

                $this->setReturnURL($this->buildURL('checkout'));
                $this->setHardRedirect();
                return;
            }

            $this->printAJAX($result);
            $this->silent = true;
            $this->setSuppressOutput(true);
        }
    }

    public function doActionOnApprove()
    {
        $paymentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);
        $processor     = $paymentMethod->getProcessor();

        $requestData = \XLite\Core\Request::getInstance()->data;

        $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
            ->findOneByCell('PaypalOrderId', $requestData['orderID']);

        if ($transaction) {
            $processor->onApprove($transaction, \XLite\Core\Request::getInstance()->data);

            $orderDetails = $processor->getPaypalOrder($transaction->getDataCell('PaypalOrderId')->getValue());
            if ($orderDetails) {
                $this->requestData = [
                    'email'          => $orderDetails->payer->email_address ?? '',
                    'create_profile' => false,
                ];

                $purchaseUnit = $orderDetails->purchase_units[0] ?? [];
                if (isset($purchaseUnit->shipping)) {
                    $address = $purchaseUnit->shipping->address;

                    $street = ($address->address_line_1 ?? '')
                        . (isset($address->address_line_2) && $address->address_line_2 !== 'n/a'
                            ? (' ' . $address->address_line_2)
                            : '');

                    $this->requestData['shippingAddress'] = [
                        'name'         => $purchaseUnit->shipping->name->full_name ?? '',
                        'street'       => $street,
                        'country_code' => $address->country_code ?? '',
                        'state'        => $address->admin_area_1 ?? '',
                        'city'         => $address->admin_area_2 ?? '',
                        'zipcode'      => $address->postal_code ?? '',
                    ];

                    $this->requestData['billingAddress'] = $this->requestData['shippingAddress'];
                    $this->requestData['same_address']   = true;
                }

                $profile = $this->getProfile();
                if (!$profile && $this->getCart()) {
                    $profile = $this->getCart()->getProfile();
                }

                if (!\XLite\Core\Auth::getInstance()->isLogged() && (!$profile || !$profile->getLogin())) {
                    $this->updateProfile();
                }

                $modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
                if ($modifier && $modifier->canApply()) {
                    $this->updateShippingAddress();
                }

                $this->updateBillingAddress();

                $this->setCheckoutAvailable();

                $this->updateCart();
            }

            \XLite\Core\Database::getEM()->flush();

            $this->setHardRedirect();
            $this->setReturnURL($this->buildURL('checkout'));
        } else {
            \XLite\Core\TopMessage::addWarning('Transaction not fond');
        }
    }

    /**
     * https://developer.paypal.com/docs/checkout/reference/server-integration/authorize-transaction/#on-the-server
     */
    public function doActionAuthorizeOrder()
    {
    }

    /**
     * https://developer.paypal.com/docs/checkout/reference/server-integration/capture-transaction/#on-the-server
     */
    public function doActionCaptureOrder()
    {
    }

    protected function getCreateOrderData()
    {
    }
}
