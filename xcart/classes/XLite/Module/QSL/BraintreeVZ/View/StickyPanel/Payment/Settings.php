<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\StickyPanel\Payment;

/**
 * Payment method settings sticky panel
 */
class Settings extends \XLite\View\StickyPanel\Payment\Settings implements \XLite\Base\IDecorator
{
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
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if ($this->isBraintreePaymentMethod()) {

            $backLink = array_pop($list);

            $list['synchronize'] = $this->getWidget(
                array(
                	\XLite\View\Button\AButton::PARAM_LABEL => static::t('Synchronize'),
                    \XLite\View\Button\AButton::PARAM_STYLE => 'btn regular-button always-enabled',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('braintree_account', 'synchronize'),
                ),
                '\XLite\View\Button\SimpleLink'
            );

            $list['addons-list'] = $backLink;
        }

        return $list;
    }
}

