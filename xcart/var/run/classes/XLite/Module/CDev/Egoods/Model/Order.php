<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model;
use XLite\Model\Order\Status\Shipping;

/**
 * Order
 * @Decorator\Before("CDev\Egoods")
 */
abstract class Order extends \XLite\Module\CDev\GoogleAnalytics\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Get Private attachments list
     *
     * @return array
     */
    public function getPrivateAttachments()
    {
        $list = [];
        foreach ($this->getItems() as $item) {
            $list = array_merge($list, $item->getPrivateAttachments()->toArray());
        }

        return $list;
    }

    /**
     * Get downloadable Private attachments list
     *
     * @param bool $availableOnly
     *
     * @return array
     */
    public function getDownloadAttachments($availableOnly = true)
    {
        $list = [];
        foreach ($this->getItems() as $item) {
            $list = array_merge($list, $item->getDownloadAttachments($availableOnly));
        }

        return $list;
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        $this->initializeAttachments();

        if (
            $this->getPrivateAttachments()
            && \XLite\Core\Config::getInstance()->CDev->Egoods->approve_before_download
            && $this->getShippingStatusCode() !== Shipping::STATUS_WAITING_FOR_APPROVE
        ) {
            $this->setShippingStatus(Shipping::STATUS_WAITING_FOR_APPROVE);
        }
    }

    /**
     * Initialize provate attachments
     *
     * @return void
     */
    protected function initializeAttachments()
    {
        if (!$this->getPrivateAttachments()) {
            foreach ($this->getItems() as $item) {
                $item->createPrivateAttachments();
            }
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processProcess()
    {
        $this->renewPrivateAttachments();

        parent::processProcess();
    }

    /**
     * Sets download keys for private attachments inside the order
     */
    protected function renewPrivateAttachments()
    {
        $this->initializeAttachments();

        foreach ($this->getPrivateAttachments() as $attachment) {
            $attachment->renew();
        }
    }
}
