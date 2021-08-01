<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace XLite\Module\QSL\BraintreeVZ\Controller\Customer;

/**
 * Braintree credit cards 
 */
class BraintreeCreditCards extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Saved Braintree credit cards');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return !empty(\XLite\Core\Request::getInstance()->widget);
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Saved Braintree credit cards');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode('My account');
    }

    /** 
     * Display or not the info and action to set default credit card
     *
     * @return bool
     */
    public function isDisplayDefaultAction()
    {
        return count($this->getProfile()->getBraintreeCreditCardsHash()) > 1;
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
                $this->getProfile(),
                $request->default_card_token
            );

        } elseif ($request->remove_card_token) {

            // Remove payment method/credit card
            $result = $braintree->removeCardByToken(
                $this->getProfile(),
                $request->remove_card_token
            );
        }

        if ($result) {
            \XLite\Core\TopMessage::getInstance()->addInfo('Operation successful');
        }
    }
}
