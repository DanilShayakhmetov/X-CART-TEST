<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

use XLite\Core\Request;
use XLite\Module\XC\MailChimp\Core;
use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;
use XLite\Module\XC\MailChimp\Main;

/**
 * Class represents an order
 */
abstract class Cart extends \XLite\Model\Cart implements \XLite\Base\IDecorator
{
    protected static $mcNewCartFlag;

    /**
     * Method to access a singleton
     *
     * @param boolean $doCalculate Flag for cart recalculation OPTIONAL
     *
     * @return \XLite\Model\Cart
     */
    public static function getInstance($doCalculate = true)
    {
        $cart = parent::getInstance($doCalculate);

        if (!isset(static::$mcNewCartFlag)) {
            static::$mcNewCartFlag = !$cart->isPersistent();
        }

        return $cart;
    }

    /**
     * Check if mc should create new cart without checking
     *
     * @return boolean
     */
    public static function isMcNewCart()
    {
        return static::$mcNewCartFlag;
    }

    /**
     * Prepare order before save data operation
     *
     * @PrePersist
     * @PreUpdate
     */
    public function prepareBeforeSave()
    {
        parent::prepareBeforeSave();

        if ($this->needUpdateMailchimpCart()
            && Main::isMailChimpAbandonedCartEnabled()
            && !$this->getOrderNumber()
            && $this->getProfile()
            && $this->getProfile()->getEmail()
        ) {
            MailChimpQueue::getInstance()->addAction(
                'cartUpdate',
                new Core\Action\CartUpdate($this)
            );
        }
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        /** @see \XLite\Module\XC\MailChimp\Model\Order::isECommerce360Order() */
        if (($this->isECommerce360Order()
                || Main::getStoreForDefaultAutomation())
            && Main::isMailChimpECommerceConfigured()
        ) {
            try {
                $mcCore = Core\MailChimp::getInstance();

                $mcCore->createOrder($this);

                $mcCore->removeCart($this);

            } catch (\Exception $e) {
                \XLite\Logger::getInstance()->log($e->getMessage());
            }
        }

        $profile = $this->getAvailableProfile();
        if ($profile
            && $profile->hasMailChimpSubscriptions()
        ) {
            $profile->checkSegmentsConditions();
        }
    }

    /**
     * @return bool
     */
    protected function needUpdateMailchimpCart()
    {
        $request = \XLite\Core\Request::getInstance();
        $result  = true;

        if ($request->widget === '\XLite\View\Minicart'
            || ($request->target === 'checkout'
                && $request->action !== 'shipping'
            )
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get available profile
     *
     * @return \XLite\Module\XC\MailChimp\Model\Profile|\XLite\Model\Profile
     */
    protected function getAvailableProfile(): \XLite\Model\Profile
    {
        return $this->getOrigProfile() ? $this->getOrigProfile() : $this->getProfile();
    }
}
