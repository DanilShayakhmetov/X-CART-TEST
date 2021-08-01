<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Conversation
 *
 * @Entity
 * @Table (name="conversations")
 */
class Conversation extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Messages
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\VendorMessages\Model\Message", mappedBy="conversation", cascade={"all"})
     * @OrderBy   ({"date" = "ASC"})
     */
    protected $messages;

    /**
     * Order
     *
     * @var \XLite\Model\Order
     *
     * @OneToOne (targetEntity="XLite\Model\Order", inversedBy="conversation")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Conversation members
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Model\Profile", inversedBy="coupons")
     * @JoinTable (name="conversation_members",
     *      joinColumns={@JoinColumn (name="conversation_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")}
     * )
     */
    protected $members;

    /**
     * @inheritdoc
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Return Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set messages
     *
     * @param \Doctrine\Common\Collections\Collection $messages Messages
     *
     * @return static
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Set messages
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message Message
     *
     * @return $this
     */
    public function addMessage($message)
    {
        $this->messages->add($message);

        return $this;
    }

    /**
     * Return Members
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set Members
     *
     * @param mixed $members
     *
     * @return $this
     */
    public function setMembers($members)
    {
        $this->members = $members;
        return $this;
    }

    /**
     * Set Members
     *
     * @param \XLite\Model\Profile $member
     *
     * @return $this
     */
    public function addMember($member)
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }
        return $this;
    }

    /**
     * Return Order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set Order
     *
     * @param \XLite\Model\Order $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Build new message
     *
     * @param \XLite\Model\Profile $author
     * @param string               $body
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    public function buildNewMessage($author, $body)
    {
        $message = new \XLite\Module\XC\VendorMessages\Model\Message();
        $message->setConversation($this);
        $message->setBody($body);

        $this->addMessage($message);

        $message->setAuthor($author);

        $message->markAsRead($author);

        return $message;
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
        foreach ($this->getMessages() as $message) {
            if (!$message->isRead($profile)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get last message
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    public function getLastMessage()
    {
        return count($this->getMessages()) > 0 ? $this->getMessages()->last() : null;
    }

    /**
     * Return conversation name
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return string
     */
    public function getName($profile = null)
    {
        if ($order = $this->getOrder()) {
            $orderNumber = $order->getOrderNumber();

            if (!$orderNumber && \XLite\Module\XC\VendorMessages\Main::isMultivendor() && $order->isChild()) {
                $orderNumber = $order->getParent()->getOrderNumber();

                if (
                    (!$profile || $order->getVendor()->getProfileId() !== $profile->getProfileId())
                    && $order->getParent()->getChildren()->count() > 1
                ) {
                    return static::t('Order X',['id' => ($orderNumber . ' - ' . ($order->getNameForMessages()))]);
                }
            }

            return static::t('Order X',['id' =>$orderNumber]);
        }

        $profile = null === $profile
            ? \Xlite\Core\Auth::getInstance()->getProfile()
            : $profile;

        $names = array_filter(array_map(function ($member) use ($profile) {
            return $profile->getProfileId() !== $member->getProfileId()
                ? $member->getNameForMessages()
                : null;
        }, $this->getMembers()->toArray()));

        return static::t('Conversation: X', [
            'members' => implode(', ', array_unique($names))
        ]);
    }

    /**
     * Check if user is member of conversation
     *
     * @param $profile
     *
     * @return bool
     */
    public function isMember($profile) {
        return $profile && $this->getMembers()->contains($profile);
    }

    /**
     * Is user has access to conversation(non-order conversations)
     *
     * @param null $profile
     *
     * @return bool
     */
    public function checkAccess($profile = null)
    {
        $auth = \XLite\Core\Auth::getInstance();
        $result = false;

        $profile = $profile ?: $auth->getProfile();

        if ($profile && !$this->getOrder()) {
            $result = $auth->isPermissionAllowed('manage conversations') || $this->isMember($profile);
        }

        return $result;
    }
}
