<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\GenerateData\Generators;

/**
 * Class Order
 * @package XLite\Console\Command\GenerateData\Generators
 */
class Order
{
    public function __construct()
    {
        $this->productsCount = \XLite\Core\Database::getRepo('XLite\Model\Product')->count();
    }

    /**
     * @param \XLite\Model\Profile        $profile
     * @param \XLite\Model\Currency       $currency
     * @param \XLite\Model\Payment\Method $method
     * @param                             $itemsCount
     *
     * @return \XLite\Model\Order
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function generate(
        \XLite\Model\Profile $profile,
        \XLite\Model\Currency $currency,
        \XLite\Model\Payment\Method $method,
        $itemsCount
    ) {
        $paymentStatus = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Payment')->findOneByCode('P');
        $shippingStatus = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Shipping')->findOneByCode('D');

        $profileClone = $profile->cloneEntity();

        /** @var \XLite\Model\Order $order */
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->insert(
            [
                'origProfile'    => $profile,
                'profile'        => $profileClone,
                'currency'       => $currency,
                'paymentStatus'  => $paymentStatus,
                'shippingStatus' => $shippingStatus
            ],
            false
        );

        $order->setOrderNumber(
            \XLite\Core\Database::getRepo('XLite\Model\Order')->findNextOrderNumber()
        );
        $order->setPaymentMethod($method);

        $profileClone->setOrder($order);
        for ($n = 0; $n < $itemsCount; $n++) {
            $item = $this->createRandomOrderItem($order);
            $order->addItems($item);
            $item->renew();
        }
        \XLite\Core\Database::getEM()->flush();

        $order->renewPaymentMethod();
        $order->renewShippingMethod();
        $order->calculate();

        return $order;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return \XLite\Model\OrderItem
     */
    protected function createRandomOrderItem(\XLite\Model\Order $order)
    {
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->findFrame(
            mt_rand(0, $this->productsCount - 1),
            1
        );
        $product = reset($product);

        /** @var \XLite\Model\OrderItem $item */
        $item = \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->insert(
            array(
                'order'   => $order,
                'product' => $product,
            ),
            false
        );

        return $item;
    }
}
