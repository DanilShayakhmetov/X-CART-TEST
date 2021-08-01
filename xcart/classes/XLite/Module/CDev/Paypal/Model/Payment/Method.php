<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment;

use \XLite\Module\CDev\Paypal;

/**
 * Payment method model
 */
class Method extends \XLite\Model\Payment\Method implements \XLite\Base\IDecorator
{
    /**
     * Get payment processor class
     *
     * @return string
     */
    public function getClass()
    {
        $class = parent::getClass();

        if (Paypal\Main::PP_METHOD_EC == $this->getServiceName()) {
            $className = 'XLite\\' . $class;
            /** @var \XLite\Model\Payment\Base\Processor $processor */
            $processor = \XLite\Core\Operator::isClassExists($className) ? $className::getInstance() : null;

            if ($this->isForceMerchantAPI($processor)) {
                $class = 'Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPI';
            }
        }

        if (Paypal\Main::PP_METHOD_PC == $this->getServiceName()) {
            $className = 'XLite\\' . $class;
            /** @var \XLite\Model\Payment\Base\Processor $processor */
            $processor = \XLite\Core\Operator::isClassExists($className) ? $className::getInstance() : null;

            if (
                $this->getExpressCheckoutPaymentMethod()
                && $this->getExpressCheckoutPaymentMethod()->isForceMerchantAPI($processor)
            ) {
                $class = 'Module\CDev\Paypal\Model\Payment\Processor\PaypalCreditMerchantAPI';
            }
        }

        return $class;
    }

    /**
     * Get payment method setting by its name
     *
     * @param string $name Setting name
     *
     * @return string
     */
    public function getSetting($name)
    {
        if (Paypal\Main::PP_METHOD_EC === $this->getServiceName() && $this->isForcedEnabled()) {
            $parentMethod = $this->getProcessor()->getParentMethod();
            $result = $parentMethod->getSetting($name);

        } elseif (Paypal\Main::PP_METHOD_PC === $this->getServiceName()) {
            $parentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

            $result = $this->isForwardingAllowedForSetting($name) && $parentMethod->getSetting($name)
                ? $parentMethod->getSetting($name)
                : parent::getSetting($name);

        } else {
            $result = parent::getSetting($name);
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function isForwardingAllowedForSetting($name)
    {
        $parentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

        return $name !== 'email' || 'email' === $parentMethod->getSetting('api_type');
    }

    /**
     * Additional check for PPS
     *
     * @return boolean
     */
    public function isEnabled()
    {
        $result = parent::isEnabled();

        if ($result && Paypal\Main::PP_METHOD_PPS == $this->getServiceName()) {
            $result = !$this->getProcessor()->isPaypalAdvancedEnabled();
        }

        if ($result && Paypal\Main::PP_METHOD_PC == $this->getServiceName()) {
            $result = Paypal\Main::isExpressCheckoutEnabled() && $this->getSetting('enabled');
        }

        return $result;
    }

    /**
     * Set 'added' property
     *
     * @param boolean $added Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setAdded($added)
    {
        $result = parent::setAdded($added);

        if (Paypal\Main::PP_METHOD_EC == $this->getServiceName()) {
            if (!$added) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    array(
                        'category' => 'CDev\Paypal',
                        'name'     => 'show_admin_welcome',
                        'value'    => 'N',
                    )
                );
            }
        }

        return $result;
    }

    /**
     * Get Express Checkout payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getExpressCheckoutPaymentMethod()
    {
        return Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);
    }

    /**
     * Is forced Merchant API for Paypal Express
     * https://developer.paypal.com/docs/classic/api/#merchant
     *
     * @param \XLite\Model\Payment\Base\Processor $processor Payment processor
     *
     * @return boolean
     */
    protected function isForceMerchantAPI($processor)
    {
        $parentMethod = $processor
            ? $processor->getParentMethod()
            : null;

        return $processor
            && !$processor->isForcedEnabled($this)
            && (
                'email' === parent::getSetting('api_type')
                || 'paypal' === parent::getSetting('api_solution')
                || ($parentMethod && !$processor->isConfigured($parentMethod))
            );
    }

    /**
     * Get warning note
     *
     * @return string
     */
    public function getWarningNote()
    {
        $message = parent::getWarningNote();

        if ($this->getProcessor()
            && in_array($this->getServiceName(), [Paypal\Main::PP_METHOD_PAD, Paypal\Main::PP_METHOD_PCP, Paypal\Main::PP_METHOD_PPA, Paypal\Main::PP_METHOD_EC], true)
            && $this->getProcessor()->getWarningNote($this)
        ) {
            $message = $this->getProcessor()->getWarningNote($this);
        }

        return $message;
    }

    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        $message = parent::getNotSwitchableReason();

        if ($this->getProcessor()
            && Paypal\Main::PP_METHOD_PAD === $this->getServiceName()
            && $this->getProcessor()->getWarningNote($this)
        ) {
            $message = static::t(
                'To enable this payment method, you need <Multi-vendor> module installed.', [
                    'link'  => \XLite::getInstance()->getServiceURL(
                        '#/available-addons', null, [
                            'tag' => 'Catalog Management',
                            'search' => 'Multi-vendor'
                        ]
                    )
                ]
            );
        }

        if ($this->getProcessor()
            && Paypal\Main::PP_METHOD_PFM === $this->getServiceName()
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'multi-vendor':
                    $message = static::t(
                        'To enable this payment method, you need <Multi-vendor> module installed.', [
                            'link'  => \XLite::getInstance()->getServiceURL(
                                '#/available-addons', null, [
                                    'tag' => 'Catalog Management',
                                    'search' => 'Multi-vendor'
                                ]
                            )
                        ]
                    );
                    break;

                case 'https':
                    $message = static::t(
                        'Payments with this payment method are not allowed because HTTPS is not configured',
                        [
                            'url' => \XLite\Core\Converter::buildURL('https_settings')
                        ]
                    );
                    break;
            }
        }

        if ($this->getProcessor()
            && Paypal\Main::PP_METHOD_PCP === $this->getServiceName()
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'https':
                    $message = static::t(
                        'Payments with this payment method are not allowed because HTTPS is not configured',
                        [
                            'url' => \XLite\Core\Converter::buildURL('https_settings')
                        ]
                    );
                    break;
                case 'conflict':
                    $message = static::t(
                        'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                    );
                    break;
            }
        }

        if ($this->getProcessor()
            && Paypal\Main::PP_METHOD_EC === $this->getServiceName()
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'conflict':
                    $message = static::t(
                        'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                    );
                    break;
            }
        }

        if ($this->getProcessor()
            && Paypal\Main::PP_METHOD_PPA === $this->getServiceName()
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'conflict':
                    $message = static::t(
                        'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                    );
                    break;
            }
        }

        return $message;
    }
}
