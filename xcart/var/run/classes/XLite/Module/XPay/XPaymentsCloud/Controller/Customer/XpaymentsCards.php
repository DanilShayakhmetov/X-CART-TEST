<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Customer;

use \XLite\Core\Request;
use \XLite\Core\TopMessage;

/**
 * X-Payments Saved cards 
 */
class XpaymentsCards extends \XLite\Controller\Customer\ACustomer
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
        if ($this->checkAccess() && Request::getInstance()->widget_title) {
            $result = Request::getInstance()->widget_title;
        } else {
            $result = static::t('Saved cards');
        }

        return $result;
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return Request::getInstance()->widget && $this->checkAccess();
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
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
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
     * Get customer profile (wrapper)
     *
     * @return \XLite\Module\XPay\XPaymentsCloud\Model\Profile
     */
    protected function getCustomerProfile()
    {
        return $this->getProfile();
    }

    /**
     * Check if it is possible to add a new card
     *
     * @return bool
     */
    public function isSaveCardsAllowed()
    {
        $settings = $this->getCustomerProfile()->getXpaymentsTokenizationSettings();
        return $settings['tokenizationEnabled'] && !$settings['limitReached'];
    }

    /**
     * Check if it is possible to add a new card
     *
     * @return bool
     */
    public function isSaveCardsLimitReached()
    {
        $settings = $this->getCustomerProfile()->getXpaymentsTokenizationSettings();
        return $settings['limitReached'];
    }

    /**
     * Returns tokenize card amount configured in XP
     *
     * @return bool
     */
    public function getCardSetupAmount()
    {
        $settings = $this->getCustomerProfile()->getXpaymentsTokenizationSettings();
        return $settings['tokenizeCardAmount'];
    }

    /**
     * Returns array with customer cards
     *
     * @return array
     */
    public function getCards()
    {
        return $this->getCustomerProfile()->getXpaymentsCards();
    }

    /**
     * Remove X-Payments saved card
     *
     * @return void
     */
    protected function doActionRemove()
    {
        $profile = $this->getCustomerProfile();

        $cardId = Request::getInstance()->card_id;

        if ($profile->removeXpaymentsCard($cardId)) {
            TopMessage::addInfo('Saved card has been deleted');
        } else {
            TopMessage::addError('Failed to delete saved card');
        }

        $this->reloadPage();
    }

    /**
     * Remove X-Payments saved card
     *
     * @return void
     */
    protected function doActionCardSetup()
    {
        \XLite\Core\Session::getInstance()->xpaymentsCardSetupData = null;

        /** @var \XLite\Module\XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud $processor */
        $processor = \XLite\Module\XPay\XPaymentsCloud\Main::getPaymentMethod()->getProcessor();

        /** @var \XLite\Model\Address $address */
        $address = \XLite\Core\Database::getRepo('XLite\Model\Address')->find(Request::getInstance()->addressId);

        if (
            $address
            && $address->getProfile()->getProfileId() === $this->getCustomerProfile()->getProfileId()
        ) {
            $response = $processor->processCardSetup(
                Request::getInstance()->xpaymentsToken,
                $this->getCustomerProfile(),
                $address,
                \XLite::getInstance()->getShopURL($this->buildURL('xpayments_cards', 'continue_card_setup'))
            );
        } else {
            TopMessage::addError('Invalid profile address!');
        }

        if (
            !empty($response)
            && !is_null($response->redirectUrl)
            && $response->getPayment()
        ) {
            // Redirect to 3-D Secure is required
            \XLite\Core\Session::getInstance()->xpaymentsCardSetupData = [
                'redirectUrl' => $response->redirectUrl,
                'xpid' => $response->getPayment()->xpid,
            ];
            $redirectUrl = $this->buildURL('checkoutPayment', '', [ 'mode' => 'CardSetup' ]);

        } else {
            // No 3-D Secure, so card is processed already or error happened
            $redirectUrl = $this->buildURL('xpayments_cards');
        }

        $this->reloadPage($redirectUrl);
    }

    /**
     * Remove X-Payments saved card
     *
     * @return void
     */
    protected function doActionContinueCardSetup()
    {
        /** @var \XLite\Module\XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud $processor */
        $processor = \XLite\Module\XPay\XPaymentsCloud\Main::getPaymentMethod()->getProcessor();

        $data = \XLite\Core\Session::getInstance()->xpaymentsCardSetupData;
        \XLite\Core\Session::getInstance()->xpaymentsCardSetupData = null;
        if (!empty($data['xpid'])) {
            $processor->processContinueCardSetup($data['xpid'], $this->getCustomerProfile());
        } else {
            TopMessage::addError('Transaction was lost!');
        }

        $this->reloadPage();
    }

    /**
     * Sets hard redirect to reload the page
     *
     * @param string $url
     */
    protected function reloadPage($url = null)
    {
        if (is_null($url)) {
            $url = $this->buildURL('xpayments_cards');
        }

        $this->setHardRedirect();
        $this->setReturnURL($url);
        $this->doRedirect();
    }

}
