<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Module\XPay\XPaymentsCloud\Controller\Admin;

/**
 * X-Payments saved credit cards
 */
class XpaymentsCards extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Edit profile');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        $profile = (null !== $this->getCustomerProfile())
            ? $this->getCustomerProfile()
            : \XLite\Core\Auth::getInstance()->getProfile();

        $isAnonymous = $profile->getAnonymous();

        $checkACL = parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage users');

        return $checkACL && !$isAnonymous;
    }

    /**
     * Get customer profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        $profileId = \XLite\Core\Request::getInstance()->profile_id;
        if (empty($profileId)) {
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }

        /** @var \XLite\Model\Profile $profile */
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
            ->find(intval($profileId));

        return $profile;
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

        $cardId = \XLite\Core\Request::getInstance()->card_id;

        if ($profile->removeXpaymentsCard($cardId)) {
            \XLite\Core\TopMessage::addInfo('Saved card has been deleted');
        } else {
            \XLite\Core\TopMessage::addError('Failed to delete saved card');
        }

        $this->setHardRedirect();
        $this->setReturnURL($this->buildURL('xpayments_cards'), array('profile_id' => $profile->getProfileId()));
        $this->doRedirect();
    }
}
