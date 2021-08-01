<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Order;

use XLite\Model\Order;
use XLite\Model\Order\Status\Shipping as ShippingStatus;
use XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment;

/**
 * User files
 */
class Files extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->getOrdersWithFiles()) {
            $list[] = 'modules/CDev/Egoods/files_page.less';
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_CSS => ['css/files.css']
        ]);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Egoods/files_page_template.twig';
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    protected function isShowOrderUnavailableMark($order)
    {
        return !in_array(
            $order->getPaymentStatusCode(),
            [
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_PART_PAID
            ]
        ) || $order->getShippingStatusCode() === ShippingStatus::STATUS_WAITING_FOR_APPROVE;
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    protected function getOrderUnavailableMark($order)
    {
        if (!in_array(
            $order->getPaymentStatusCode(),
            [
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_PART_PAID
            ]
        )) {
            return $order->getPaymentStatus()->getName();
        } elseif ($order->getShippingStatusCode() === ShippingStatus::STATUS_WAITING_FOR_APPROVE) {
            return $order->getShippingStatus()->getName();
        }

        return static::t('Unavailable');
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    protected function getOrderMarkClasses($order)
    {
        $classes = [];

        if ($order->isQueued() || $order->getShippingStatusCode() === ShippingStatus::STATUS_WAITING_FOR_APPROVE) {
            $classes[] = 'green';
        }

        return implode(' ', $classes);
    }

    /**
     * @param PrivateAttachment $attachment
     *
     * @return bool
     */
    protected function isShowAttachmentUnavailableMark($attachment)
    {
        return !$attachment->isAvailable() && !$this->isShowOrderUnavailableMark($attachment->getItem()->getOrder());
    }

    /**
     * @param PrivateAttachment $attachment
     *
     * @return string
     */
    protected function getAttachmentUnavailableMark($attachment)
    {
        if ($attachment->isExpired()) {
            return static::t('Expired');
        }

        if ($attachment->getAttachment() && !$attachment->getAttachment()->getStorage()->isFileExists()) {
            return static::t('File not found');
        }

        return static::t('Unavailable');
    }

    /**
     * @param PrivateAttachment $attachment
     *
     * @return string
     */
    protected function getMimeName($attachment)
    {
        if (
            $attachment
            && $attachment->getAttachment()
            && $attachment->getAttachment()->getStorage()
        ) {
            return $attachment->getAttachment()->getStorage()->getMimeName();
        }

        return '';
    }

    /**
     * @param PrivateAttachment $attachment
     *
     * @return string
     */
    protected function getMimeClass($attachment)
    {
        if (
            $attachment
            && $attachment->getAttachment()
            && $attachment->getAttachment()->getStorage()
        ) {
            return $attachment->getAttachment()->getStorage()->getMimeClass();
        }

        return 'unknown';
    }

    /**
     * @param PrivateAttachment $attachment
     *
     * @return string
     */
    protected function getPublicTitle($attachment)
    {
        if (
            $attachment
        ) {
            return $attachment->getTitle();
        }

        return '';
    }

    /**
     * @param PrivateAttachment $attachment
     *
     * @return string
     */
    protected function getIconType($attachment)
    {
        if (
            $attachment
            && $attachment->getAttachment()
        ) {
            return $attachment->getAttachment()->getIconType();
        }

        return 'default';
    }
}

