<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model;
use XLite\Core\Converter;
use XLite\Model\Order\Status\Shipping;

/**
 * Order
 */
 class Order extends \XLite\Module\CDev\USPS\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Returns true if the order has any pin codes
     *
     * @return boolean
     */
    public function hasPinCodes()
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->countPinCodes() || $item->getProduct()->getPinCodesEnabled()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Assign PIN codes to the order items
     *
     * @return void
     */
    public function acquirePINCodes()
    {
        $missingCount = 0;
        $lastProductId = null;
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getPinCodesEnabled() && !$item->countPinCodes()) {
                $item->acquirePinCodes();
                $missingCount += $item->countMissingPinCodes();
                $lastProductId = $item->getProduct()->getId() ?: $lastProductId;
            }
        }

        if ($missingCount && \XLite::isAdminZone()) {
            \XLite\Core\TopMessage::addError(
                'Could not assign X PIN codes to order #Y.',
                [
                    'count'   => $missingCount,
                    'orderId' => $this->getOrderNumber(),
                    'link'    => Converter::buildFullURL('product', '', [
                        'page'       => 'pin_codes',
                        'product_id' => $lastProductId,
                    ]),
                ]
            );
        }
    }

    /**
     * Check if order is allowed to acquire and sell pin codes
     *
     * @return bool
     */
    public function isAllowToProcessPinCodes()
    {
        return $this->isProcessed() && !in_array($this->getShippingStatusCode(), [
            Shipping::STATUS_WAITING_FOR_APPROVE,
            Shipping::STATUS_WILL_NOT_DELIVER,
        ]);
    }

    /**
     * Increase / decrease item product inventory
     *
     * @param \XLite\Model\OrderItem $item      Order item
     * @param integer                $sign      Flag; "1" or "-1"
     * @param boolean                $register  Register in order history OPTIONAL
     *
     * @return integer
     */
    protected function changeItemInventory($item, $sign, $register = true)
    {
        $result = parent::changeItemInventory($item, $sign, $register);

        /** @var \XLite\Module\CDev\PINCodes\Model\Product $product */
        $product = $item->getObject();

        if ($product
            && $product->getPinCodesEnabled()
            && !$product->getAutoPinCodes()
        ) {
            $product->syncAmount();
        }

        return $result;
    }

    /**
     * Process PIN codes 
     * 
     * @return void
     */
    public function processPINCodes()
    {
        if ($this->isAllowToProcessPinCodes()) {
            $missingCount = 0;
            $lastProductId = null;
            foreach ($this->getItems() as $item) {
                if ($item->getProduct()->getPinCodesEnabled()) {
                    if (!$item->countPinCodes()) {
                        $item->acquirePinCodes();
                        $missingCount += $item->countMissingPinCodes();
                        $lastProductId = $item->getProduct()->getId() ?: $lastProductId;
                    }

                    if ($item->countPinCodes()) {
                        $item->salePinCodes();
                    }
                }
            }

            if ($missingCount && \XLite::isAdminZone()) {
                \XLite\Core\TopMessage::addError(
                    'Could not assign X PIN codes to order #Y.',
                    [
                        'count'   => $missingCount,
                        'orderId' => $this->getOrderNumber(),
                        'link'    => Converter::buildFullURL('product', '', [
                            'page'       => 'pin_codes',
                            'product_id' => $lastProductId,
                        ]),
                    ]
                );
            }
        }
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        $this->acquirePINCodes();

        if ($this->hasPinCodes()
            && \XLite\Core\Config::getInstance()->CDev->PINCodes->approve_before_download
            && $this->getShippingStatusCode() !== Shipping::STATUS_WAITING_FOR_APPROVE
        ) {
            $this->setShippingStatus(Shipping::STATUS_WAITING_FOR_APPROVE);
        }

        parent::processSucceed();
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processProcess()
    {
        $this->processPINCodes();

        parent::processProcess();
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processDeclinePIN()
    {
        $this->releasePINCodes();

        if ($this->hasPinCodes()) {
            parent::processDecline();
        }
    }

    /**
     * Release PIN codes linked to order items
     *
     * @return void
     */
    protected function releasePINCodes()
    {
        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getPinCodes()) {
                $item->releasePINCodes();
            }
        }
    }
}
