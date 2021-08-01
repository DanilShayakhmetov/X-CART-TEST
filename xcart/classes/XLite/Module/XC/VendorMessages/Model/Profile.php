<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Profile
 */
class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator
{
    /**
     * Coupons
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Module\XC\VendorMessages\Model\Conversation", mappedBy="members")
     */
    protected $conversations;

    /**
     * Add coupons
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return $this
     */
    public function addConversation($conversation)
    {
        $this->conversations[] = $conversation;
        return $this;
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConversations()
    {
        return $this->conversations;
    }

    /**
     * Count unread messages
     *
     * @return integer
     */
    public function countUnreadMessages()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->countUnread($this);
    }

    /**
     * Count unread messages for own orders
     *
     * @return integer
     */
    public function countOwnUnreadMessages()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->countOwnUnread($this);
    }

    /**
     * Get vendor name for Order messages module
     *
     * @return string
     */
    public function getNameForMessages()
    {
        if (\XLite\Module\XC\VendorMessages\Main::isMultivendor() && $this->getVendor()) {
            return $this->getVendorNameForMessages();
        }

        return $this->isAdmin()
            ? \XLite\Core\Config::getInstance()->Company->company_name
            : $this->getName();
    }

    /**
     * Get vendor name for Order messages module
     *
     * @return string
     */
    public function getVendorNameForMessages()
    {
        return $this->getVendor()->getCompanyName() ?: $this->getName();
    }
}