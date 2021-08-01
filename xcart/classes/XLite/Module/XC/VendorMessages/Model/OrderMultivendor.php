<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;
use XLite\Module\XC\VendorMessages\Main;

/**
 * Order
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Has opened disputes flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $is_opened_dispute = false;

    /**
     * Get opened dispute flag
     *
     * @return boolean
     */
    public function isOpenedDispute()
    {
        return $this->getIsOpenedDispute();
    }

    /**
     * Get opened dispute flag
     *
     * @return boolean
     */
    public function getIsOpenedDispute()
    {
        return $this->is_opened_dispute;
    }

    /**
     * Set opened dispute flag
     *
     * @param boolean $is_opened_dispute Opened dispute flag
     *
     * @return OrderMultivendor
     */
    public function setIsOpenedDispute($is_opened_dispute)
    {
        $this->is_opened_dispute = $is_opened_dispute;

        return $this;
    }

    /**
     * Create new order conversation
     *
     * @return Conversation
     */
    protected function createConversation()
    {
        $conversation = parent::createConversation();

        if ($this->getVendor()) {
            $conversation->addMember($this->getVendor());
        }

        return $conversation;
    }

    /**
     * Count unread messages
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return integer
     */
    public function countUnreadMessages(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile
            ?: \XLite\Core\Auth::getInstance()->getProfile()
                ?: $this->getOrigProfile();

        if (
            Main::isWarehouse()
            && Main::isVendorAllowedToCommunicateInWarehouse()
            && $this->isParent()
        ) {
            $count = 0;
            foreach ($this->getChildren() as $order) {
                $count += $order->countUnreadMessages($profile);
            }

            return $count;
        }

        return parent::countUnreadMessages($profile);
    }

    /**
     * Count unread messages (for admin)
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return integer
     */
    public function countUnreadMessagesForAdmin(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        $targetOrders = [$this];

        if (
            Main::isWarehouse()
            && Main::isVendorAllowedToCommunicate()
            && !$this->getParent()
        ) {
            if (!$this->getIsOpenedDispute()) {
                $targetOrders = [];
            }

            foreach ($this->getChildren() as $order) {
                if ($order->getIsOpenedDispute()) {
                    $targetOrders[] = $order;
                }
            }

            $count = 0;
            foreach ($targetOrders as $order) {
                foreach ($order->getMessages() as $message) {
                    if (!$message->isRead($profile)) {
                        $count++;
                    }
                }
            }

        } elseif (
            Main::isWarehouse()
            && Main::isVendorAllowedToCommunicate()
        ) {
            $count = $this->getIsOpenedDispute() ? parent::countUnreadMessages($profile) : 0;

        } else {
            $count = parent::countUnreadMessages($profile);
        }

        return $count;
    }

    /**
     * Count unread messages (only own)
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return integer
     */
    public function countOwnUnreadMessages(\XLite\Model\Profile $profile = null)
    {
        return parent::countUnreadMessages($profile);
    }


}
