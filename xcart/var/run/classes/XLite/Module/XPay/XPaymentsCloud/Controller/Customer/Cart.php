<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Customer;

use XLite\Module\XPay\XPaymentsCloud\Core\ApplePay as XPaymentsApplePay;

/**
 * Decorated Cart controller.
 */
abstract class Cart extends \XLite\Controller\Customer\CartAbstract implements \XLite\Base\IDecorator
{
    /**
     * If item was added successfully:
     * - create virtual cart with only that product (and remove product from main cart as well)
     * - initiate Buy With Apple Pay
     * - do not add successful top message
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return void
     */
    protected function processAddItemSuccess($item)
    {
        parent::processAddItemSuccess($item);

        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithApplePay) {

            $applePayCart = $this->assignBuyWithApplePayItem($item);

            // Remove item from real cart
            $existingItem = $this->getCart()->getItemByItem($item);
            if ($existingItem->getAmount() > $item->getAmount()) {
                $existingItem->setAmount($existingItem->getAmount() - $item->getAmount());
            } else {
                $this->getCart()->getItems()->removeElement($existingItem);
            }

            \XLite\Core\TopMessage::getInstance()->unloadPreviousMessages();

            \XLite\Core\Event::xpaymentsBuyWithApplePayReady(
                [
                    'total' => $applePayCart->getTotal(),
                    'currency' => $applePayCart->getCurrency()->getCode(),
                    'shippingMethods' => XPaymentsApplePay::getApplePayShippingMethodsList($applePayCart),
                    'requiredShippingFields' => XPaymentsApplePay::getApplePayRequiredAddressFields('shipping', $applePayCart),
                    'requiredBillingFields' => XPaymentsApplePay::getApplePayRequiredAddressFields('billing', $applePayCart),
                ]
            );
        }
    }

    /**
     * Moves order item to cleared Buy With Apple Pay Cart
     *
     * @param \XLite\Model\OrderItem $orderItem
     *
     * @return \XLite\Model\Cart
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assignBuyWithApplePayItem(\XLite\Model\OrderItem $orderItem)
    {
        $applePayCart = XPaymentsApplePay::getBuyWithApplePayCart();
        $applePayCart->clear();

        if (!$applePayCart->isPersistent()) {
            \XLite\Core\Database::getEM()->persist($applePayCart);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\Session::getInstance()->buy_with_apple_pay_order_id = $applePayCart->getOrderId();
        }

        // We do not use addItem wrapper here, because it makes unwanted amount checks
        $applePayCart->addItems($orderItem);
        $orderItem->setOrder($applePayCart);

        $applePayCart->calculate();

        return $applePayCart;
    }

    /**
     * Disable redirect to cart after 'Add-to-cart' action
     *
     * @return void
     */
    protected function setURLToReturn()
    {
        if (\XLite\Core\Request::getInstance()->xpaymentsBuyWithApplePay) {
            // Skip setting redirect URL
        } else {
            parent::setURLToReturn();
        }
    }

}
