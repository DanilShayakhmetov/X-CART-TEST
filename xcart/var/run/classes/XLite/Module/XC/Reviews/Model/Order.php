<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model;

/**
 * Order
 */
 class Order extends \XLite\Module\XC\Stripe\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Order review key
     *
     * @var \XLite\Module\XC\Reviews\Model\OrderReviewKey
     *
     * @OneToOne (targetEntity="XLite\Module\XC\Reviews\Model\OrderReviewKey", mappedBy="order", cascade={"all"}, fetch="LAZY")
     */
    protected $reviewKey;

    /**
     * Process action 'Shipping status changed to Delivered'
     *
     * @return void
     */
    protected function processReviewKey()
    {
        $this->createReviewKey();
    }

    /**
     * Create review key
     *
     * @return void
     */
    public function createReviewKey()
    {
        if (\XLite\Module\XC\Reviews\Main::isCustomerFollowupEnabled()
            && $this->isOrderValidForReviewKey()
        ) {
            $reviewKey = new \XLite\Module\XC\Reviews\Model\OrderReviewKey;
            $reviewKey->setOrder($this);
            $reviewKey->setAddedDate(\XLite\Core\Converter::time());
            $reviewKey->setKeyValue(md5(sprintf('%d:%d', $this->getOrderId(), microtime(true))));
            $this->setReviewKey($reviewKey);

            \XLite\Core\Database::getEM()->persist($reviewKey);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Return true if this order is valid for create review key
     *
     * @return boolean
     */
    protected function isOrderValidForReviewKey()
    {
        return !$this->getReviewKey()
            && \XLite\Model\Order\Status\Payment::STATUS_PAID === $this->getPaymentStatusCode()
            && (
                \XLite\Model\Order\Status\Shipping::STATUS_DELIVERED === $this->getShippingStatusCode()
                || !$this->isShippable()
            );
    }

    // {{{ Default getters and setters

    /**
     * Get reviewKey
     *
     * @return \XLite\Module\XC\Reviews\Model\OrderReviewKey
     */
    public function getReviewKey()
    {
        return $this->reviewKey;
    }

    /**
     * Set reviewKey
     *
     * @param \XLite\Module\XC\Reviews\Model\OrderReviewKey $value
     * @return $this
     */
    public function setReviewKey($value)
    {
        $this->reviewKey = $value;
        return $this;
    }

    // }}}
}
