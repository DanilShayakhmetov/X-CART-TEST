<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Controller\Customer;

use XLite\Core\Operator;
use XLite\Model\Order\Status\Shipping;

/**
 * Storage
 */
abstract class Storage extends \XLite\Controller\Customer\Storage implements \XLite\Base\IDecorator
{
    /**
     * Storage private key
     *
     * @var   \XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment
     */
    protected $storageKey;

    /**
     * Get storage
     *
     * @return \XLite\Model\Base\Storage
     */
    protected function getStorage()
    {
        $storage = parent::getStorage();

        if (
            $storage
            && $storage instanceof \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage
            && $storage->getAttachment()->getPrivate()
        ) {
            $key = \XLite\Core\Request::getInstance()->key;
            $key = \XLite\Core\Database::getRepo('XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment')
                ->findOneBy(['downloadKey' => $key]);
            if (!$key || $key->getAttachment()->getId() != $storage->getAttachment()->getid() || !$key->isAvailable()) {
                $storage = null;

            } else {
                $this->storageKey = $key;
            }
        }

        return $storage;
    }

    /**
     * Create history point
     */
    protected function createHistoryPoint()
    {
        if (\XLite\Core\Config::getInstance()->CDev->Egoods->enable_history) {
            $auth = \XLite\Core\Auth::getInstance();
            $historyRepo = \XLite\Core\Database::getRepo('XLite\Module\CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint');

            /** @var \XLite\Module\CDev\Egoods\Model\Product\Attachment\AttachmentHistoryPoint $historyPoint */
            $historyPoint = $historyRepo->insert(null, false);

            if ($this->storageKey->getItem()) {
                $historyPoint->setOrder($this->storageKey->getItem()->getOrder());
            }

            $historyPoint->setLogin($auth->getProfile() ? $auth->getProfile()->getLogin() : '');
            $historyPoint->setProfile($auth->getProfile());
            $historyPoint->setAttachment($this->storageKey->getAttachment());
            $this->storageKey->getAttachment()->addHistoryPoint($historyPoint);

            if ($this->storageKey->getAttachment() && $this->storageKey->getAttachment()->getStorage()) {
                $pi = pathinfo($this->storageKey->getAttachment()->getStorage()->getStoragePath());
                $path = isset($pi['dirname']) ? $pi['dirname'] . LC_DS : '';
                $path .= $this->storageKey->getAttachment()->getStorage()->getFileName();
                $historyPoint->setPath($path);
            }

            $historyPoint->setIp(\XLite\Core\Request::getInstance()->getClientIp());
            $historyPoint->fillAdditionalDetails();
        }
    }

    /**
     * Set delivered shipping status on file download
     */
    protected function processOrderStatus()
    {
        if (
            $this->storageKey->getItem()
            && $this->storageKey->getItem()->getOrder()
            && $this->isNeedToMarkOrderAsDelivered($this->storageKey->getItem()->getOrder())
        ) {
            $order = $this->storageKey->getItem()->getOrder();

            if (in_array($order->getShippingStatusCode(), [
                Shipping::STATUS_NEW,
                Shipping::STATUS_PROCESSING,
                Shipping::STATUS_SHIPPED,
            ])) {
                $deliveredStatus = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Shipping')
                    ->findOneByCode(Shipping::STATUS_DELIVERED);

                \XLite\Core\OrderHistory::getInstance()->registerOrderDeliveredByDownload(
                    $order->getOrderId(),
                    [
                        'old' => $order->getShippingStatus() ? $order->getShippingStatus()->getName() : 'Undefined',
                        'new' => $deliveredStatus->getName(),
                    ]
                );

                $order->setRecent(false);

                $order->setShippingStatus($deliveredStatus);
                $order->setOldShippingStatus($deliveredStatus);
            }
        }
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return bool
     */
    protected function isNeedToMarkOrderAsDelivered(\XLite\Model\Order $order)
    {
        if ($order->isShippable()) {
            return false;
        }

        /** @var \XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $privateAttachment */
        foreach ($order->getPrivateAttachments() as $privateAttachment) {
            if ($privateAttachment->getAttempt() <= 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Read storage
     *
     * @param \XLite\Model\Base\Storage $storage Storage
     */
    protected function readStorage(\XLite\Model\Base\Storage $storage)
    {
        if ($this->storageKey) {
            $this->createHistoryPoint();

            $this->storageKey->incrementAttempt();

            $this->processOrderStatus();

            \XLite\Core\Database::getEM()->flush();
        }

        if ($storage instanceof \XLite\Module\CDev\Egoods\Model\Product\Attachment\Storage
            && $storage->canBeSigned()
            && !$storage->isFileAvailable()
        ) {
            Operator::redirect($storage->getSignedUrl());
            return;
        }

        parent::readStorage($storage);
    }

}

