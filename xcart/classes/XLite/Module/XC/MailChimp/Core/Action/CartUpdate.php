<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Action;

use XLite\Model\Cart;
use XLite\Module\XC\MailChimp\Core\Request\Cart as MailChimpCart;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;

class CartUpdate implements IMailChimpAction
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function execute(): void
    {
        try {
            MailChimpCart\UpdateOrCreate::scheduleAction($this->cart);
        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->log($e->getMessage());
        }
    }
}