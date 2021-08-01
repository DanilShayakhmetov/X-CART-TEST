<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Order
 */
 class Order extends \XLite\Module\XPay\XPaymentsCloud\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Conversation
     *
     * @var \XLite\Module\XC\VendorMessages\Model\Conversation
     *
     * @OneToOne (targetEntity="XLite\Module\XC\VendorMessages\Model\Conversation", mappedBy="order", cascade={"remove"})
     */
    protected $conversation;

    /**
     * Return Conversation
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Conversation
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * Set Conversation
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return $this
     */
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
        return $this;
    }

    /**
     * Return new or existing conversation
     *
     * @return Conversation
     */
    public function getOrderConversation()
    {
        if (!$this->getConversation()) {
            $conversation = $this->createConversation();

            \XLite\Core\Database::getEM()->persist($conversation);
            \XLite\Core\Database::getEM()->flush($this);
        }

        return $this->getConversation();
    }

    /**
     * Create new order conversation
     *
     * @return Conversation
     */
    protected function createConversation()
    {
        $conversation = new \XLite\Module\XC\VendorMessages\Model\Conversation;
        $conversation->setOrder($this);
        $conversation->addMember($this->getOrigProfile() ?: $this->getProfile());
        $this->setConversation($conversation);

        return $conversation;
    }

    /**
     * @return string
     */
    public function getNameForMessages()
    {
        return $this->getVendor()
            ? $this->getVendor()->getNameForMessages()
            : \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Build new message
     *
     * @param \XLite\Model\Profile $author
     * @param string               $body
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    public function buildNewMessage($author, $body = '')
    {
        if (!$author) {
            $author = $this->getOrigProfile() ?: $this->getProfile();
        }

        return $this->getOrderConversation()->buildNewMessage($author, $body);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->getOrderConversation()->getMessages();
    }

    /**
     * Check if order messages enabled
     *
     * @return bool
     */
    public function isOrderMessagesEnabled()
    {
        return true;
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
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        $count = 0;
        if ($profile && $this->getConversation() && (
                $this->getConversation()->isMember($profile)
                || $profile->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            )
        ) {
            foreach ($this->getMessages() as $message) {
                if (!$message->isRead($profile)) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
