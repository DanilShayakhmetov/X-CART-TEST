<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace XLite\Module\QSL\BraintreeVZ\Controller\Admin;

/**
 * Braintree credit cards
 */
class BraintreeCreditCards extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Braintree credit cards');
    }

    /**
     * Get customer profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        $profileId = intval(\XLite\Core\Request::getInstance()->profile_id);

        if (empty($profileId)) {
            // Use profile_id from session
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);
    }

    /**
     * Display or not the info and action to set default credit card
     *
     * @return bool
     */
    public function isDisplayDefaultAction()
    {
        return count($this->getCustomerProfile()->getBraintreeCreditCardsHash()) > 1;
    }

    /**
     * Action update
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $request = \XLite\Core\Request::getInstance();

        $braintree = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance();

        if ($request->default_card_token) {

            // Set default payment method/Credit card
            $result = $braintree->setDefaultCardByToken(
                $this->getCustomerProfile(),
                $request->default_card_token
            );

        } elseif ($request->remove_card_token) {

            // Remove payment method/credit card
            $result = $braintree->removeCardByToken(
                $this->getCustomerProfile(),
                $request->remove_card_token
            );
        }

        if ($result) {
            \XLite\Core\TopMessage::getInstance()->addInfo('Operation successful');
        }
    }
}
