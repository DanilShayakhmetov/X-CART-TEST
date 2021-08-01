<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
 namespace XLite\Module\QSL\BraintreeVZ\View\Tabs;

/**
 * Profile dialog
 */
 class AdminProfile extends \XLite\Module\XC\MailChimp\View\Tabs\AdminProfile implements \XLite\Base\IDecorator
{

    /**
     * @return array
     */
    protected function defineTabs()
    {

        $tabs = parent::defineTabs();

        if (
            $this->getProfile()
            && \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->isDisplayCardsTab()
        ) {
            $tabs['braintree_credit_cards'] = array(
                 'weight'   => 1100,
                 'title'    => static::t('Braintree credit cards'),
                 'template' => 'modules/QSL/BraintreeVZ/account/braintree_credit_cards.twig',
            );
        }

        return $tabs;
    }
}
