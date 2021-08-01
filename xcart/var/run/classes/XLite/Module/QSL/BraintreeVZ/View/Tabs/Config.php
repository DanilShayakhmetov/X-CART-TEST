<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\Tabs;

/**
 * Tabs related to payment settings
 * @ListChild (list="admin.center", zone="admin")
 */
class Config extends \XLite\View\Tabs\ATabs
{
    /**
     * Hack against X-Cart.
     * Config section should be visible only once, when the it'is displayed second time
     */
    protected static $_visible = false;

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'payment_method';
        $list[] = 'braintree_account';  

        return $list;
    }

    /**
     * Get Braintree payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->getPaymentMethod();
    }

    /**
     * Check if this is Braintree payment method
     *
     * @return bool
     */
    protected function isBraintreePaymentMethod()
    {
        return $this->getPaymentMethod()->getMethodId() == \XLite\Core\Request::getInstance()->method_id;
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible() 
            && ($this->isBraintreePaymentMethod() || 'braintree_account' == $this->getTarget());

        if ($result && 'payment_method' == $this->getTarget() && !static::$_visible) {
            static::$_visible = true;
            $result = false;
        }

        return $result;
    }

    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        if ('payment_method' == $target) {

            $url = \XLite\Core\Converter::buildUrl(
                'payment_method',
                '',
                array('method_id' => $this->getPaymentMethod()->getMethodId()),
                \XLite::getAdminScript()
            );   

        } else {

            $url = parent::buildTabURL($target);
        }

        return $url;
    }

    /**
     * Tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = array(
            'braintree_account' => array(
                'weight'   => 200,
                'title'    => static::t('Account'),
                'widget'    => '\XLite\Module\QSL\BraintreeVZ\View\Config\Account',
            ),
        );

        if (\XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->isConfigured()) {

            $tabs['payment_method'] = array(
                'weight'   => 100,
                'title'    => static::t('Settings'),
                'widget' => '\XLite\Module\QSL\BraintreeVZ\View\Config\Settings',
            );
        }

        return $tabs;
    }
}
