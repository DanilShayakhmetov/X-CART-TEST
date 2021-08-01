<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Admin;

use \XLite\Module\CDev\Paypal;
use XLite\Module\CDev\Paypal\Model\Payment\Processor\PaypalWPS;

/**
 * Paypal settings controller
 */
class PaypalSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('method_id');

    protected $paymentMethod = null;

    /**
     * Paypal module string name for payment methods
     */
    const MODULE_NAME = 'CDev_Paypal';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $paymentMethod = $this->getPaymentMethod();

        return $paymentMethod
            ? $paymentMethod->getName()
            : '';
    }

    /**
     * Get method id from request
     *
     * @return integer
     */
    public function getMethodId()
    {
        return \XLite\Core\Request::getInstance()->method_id;
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = $this->getMethodId()
                ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($this->getMethodId())
                : null;
        }

        return $this->paymentMethod && static::MODULE_NAME === $this->paymentMethod->getModuleName()
            ? $this->paymentMethod
            : null;
    }

    /**
     * Is In-Context Boarding SignUp available
     *
     * @return boolean
     */
    public function isInContextSignUpAvailable()
    {
        $api = Paypal\Main::getRESTAPIInstance();

        return $api->isInContextSignUpAvailable();
    }

    /**
     * Get SignUp url
     *
     * @return string
     */
    public function getSignUpUrl()
    {
        return $this->getPaymentMethod()->getReferralPageURL($this->getPaymentMethod());
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if ($this->getPaymentMethod()
            && $this->getPaymentMethod()->getProcessor() instanceof PaypalWPS
        ) {
            \XLite\Core\TmpVars::getInstance()->CDevPaypalPDTNotificationVisible = false;
        }

        $request = \XLite\Core\Request::getInstance();
        if ($request->merchantIdInPayPal
            && $request->permissionsGranted === 'true'
            && (int) $request->merchantId === 0
        ) {
            $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
                \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
            );

            $paypalForMarketplacesAPI = new \XLite\Module\CDev\Paypal\Core\PaypalForMarketplacesAPI([
                'client_id'  => $method->getSetting('client_id'),
                'secret'     => $method->getSetting('secret'),
                'partner_id' => $method->getSetting('partner_id'),
                'bn_code'    => $method->getSetting('bn_code'),
                'mode'       => $method->getProcessor()->isTestMode($method) ? 'sandbox' : 'live',
            ]);

            $merchantInfo = $paypalForMarketplacesAPI->getMerchantIntegration($request->merchantIdInPayPal);

            if ($merchantInfo->isPaymentsReceivable() && $merchantInfo->isPrimaryEmailConfirmed()) {
                $method->setSetting('additional_merchant_id', $request->merchantIdInPayPal);
                \XLite\Core\Database::getEM()->flush();

                \XLite\Core\TopMessage::addInfo($request->returnMessage);

                $this->setReturnURL(
                    $this->buildURL('paypal_settings', '', ['method_id' => $method->getMethodId()])
                );
            }
        }
    }

    protected function doActionMerchantDisconnect()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
        );

        $method->setSetting('additional_merchant_id', '');
        \XLite\Core\Database::getEM()->flush();

        $this->setReturnURL(
            $this->buildURL('paypal_settings', '', ['method_id' => $method->getMethodId()])
        );
    }

    /**
     * Do action 'Update'
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        $namespace = 'XLite\Module\CDev\Paypal\View\Model';
        $className = $this->getPaymentMethod()->getServiceName();

        return $namespace . '\\' . $className;
    }

    /**
     * doActionUpdateCredentials
     *
     * @return void
     */
    protected function doActionUpdateCredentials()
    {
        $request = \XLite\Core\Request::getInstance();
        $data = array();

        if ($request->merchantIdInPayPal) {
            $apiClient = new Paypal\Core\RESTAPI();

            $data = $apiClient->getMerchantCredentials(
                Paypal\Core\RESTAPI::PARTNER_ID,
                $request->merchantIdInPayPal
            );
        }

        $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

        if ($data && isset($data['api_credentials']) && isset($data['api_credentials']['signature'])) {
            $credentials = $data['api_credentials']['signature'];

            $method->setSetting('api_type', 'api');
            $method->setSetting('api_solution', 'paypal');
            $method->setSetting('api_username', $credentials['api_user_name']);
            $method->setSetting('api_password', $credentials['api_password']);
            $method->setSetting('auth_method', 'signature');
            $method->setSetting('signature', $credentials['signature']);
            $method->setSetting('mode', 'live');
            $method->setSetting('merchantId', $data['merchant_id']);

            $method->update();

            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'CDev\Paypal',
                    'name'     => 'show_admin_welcome',
                    'value'    => 'N',
                )
            );

            \XLite\Core\TopMessage::getInstance()->addInfo(
                'Your API credentials have been successfully obtained from your PayPal account'
                . ' and saved for use by your X-Cart store.'
            );

        } else {
            \XLite\Core\TopMessage::getInstance()->addError(
                'Unfortunately, your API credentials could not be obtained from your PayPal account automatically.'
            );
        }

        $this->setReturnURL($this->buildURL('paypal_settings', '', array('method_id' => $method->getMethodId())));
    }
}
