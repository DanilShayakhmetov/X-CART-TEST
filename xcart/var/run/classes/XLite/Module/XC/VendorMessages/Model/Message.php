<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model;

/**
 * Message
 *
 * @Entity
 * @Table (name="vendor_convo_messages")
 * @HasLifecycleCallbacks
 */
class Message extends \XLite\Model\AEntity
{

    /**
     * Message types
     */
    const MESSAGE_TYPE_REGULAR       = 'regular';
    const MESSAGE_TYPE_DISPUTE_OPEN  = 'dispute_open';
    const MESSAGE_TYPE_DISPUTE_CLOSE = 'dispute_close';

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
     * Creation date
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * Body
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $body;

    /**
     * Body
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $type = self::MESSAGE_TYPE_REGULAR;

    /**
     * Order
     *
     * @var \XLite\Module\XC\VendorMessages\Model\Conversation
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\VendorMessages\Model\Conversation", inversedBy="messages")
     * @JoinColumn (name="conversation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $conversation;

    /**
     * Author
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne  (targetEntity="XLite\Model\Profile")
     * @JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $author;

    /**
     * Readers
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\VendorMessages\Model\MessageRead", mappedBy="message", cascade={"all"})
     */
    protected $readers;

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
     * Return Date
     *
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set Date
     *
     * @param int $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Return Body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set message's body
     *
     * @param string $body Body
     *
     * @return static
     */
    public function setBody($body)
    {
        $this->body = strip_tags($body);

        return $this;
    }

    /**
     * Return Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return Conversation
     *
     * @return Conversation
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * Set Conversation
     *
     * @param Conversation $conversation
     *
     * @return $this
     */
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;
        return $this;
    }

    /**
     * Return Author
     *
     * @return \XLite\Model\Profile
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set Author
     *
     * @param \XLite\Model\Profile $author
     *
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Return Readers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReaders()
    {
        return $this->readers;
    }

    /**
     * Set Readers
     *
     * @param \Doctrine\Common\Collections\Collection $readers
     *
     * @return $this
     */
    public function setReaders($readers)
    {
        $this->readers = $readers;
        return $this;
    }

    /**
     * Add reader
     *
     * @param \XLite\Module\XC\VendorMessages\Model\MessageRead $read
     *
     * @return Message
     */
    public function addReader($read)
    {
        $this->readers->add($read);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->readers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Prepare date before create entity
     *
     * @PrePersist
     */
    public function prepareDate()
    {
        $this->setDate(\XLite\Core\Converter::time());
    }

    /**
     * Get public body
     *
     * @return string
     */
    public function getPublicBody()
    {
        $body = $this->getBody();
        $body = preg_replace('/((?:https?|ftp|mailto):\/\/\S+)/Ss', '<a href="$1">$1</a>', $body);
        $body = nl2br($body);

        return $body;
    }

    /**
     * Check - message is readed or not
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return boolean
     */
    public function isRead(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();
        foreach ($this->getReaders() as $read) {
            if ($read->isOwn($profile)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check - specified profile is message's owner or not
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return boolean
     */
    public function isOwner(\XLite\Model\Profile $profile = null)
    {
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        return $profile && $this->getAuthor()->getProfileId() === $profile->getProfileId();
    }

    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthorName()
    {
        return $this->getAuthor()->getNameForMessages();
    }

    /**
     * Get author email
     *
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->getAuthor()->isAdmin()
            ? \XLite\Core\Mailer::getSiteAdministratorMail()
            : $this->getAuthor()->getLogin();
    }

    /**
     * Mark as read
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return false|\XLite\Module\XC\VendorMessages\Model\MessageRead
     */
    public function markAsRead(\XLite\Model\Profile $profile = null)
    {
        $result = false;
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        if (
            $profile
            && $this->getAuthor()
            && !$this->isRead($profile)
        ) {
            $read = new \XLite\Module\XC\VendorMessages\Model\MessageRead;
            $read->setReader($profile);
            $this->addReader($read);
            $read->setMessage($this);
            $result = $read;
        }

        return $result;
    }

    /**
     * Send message
     *
     * @return static
     */
    public function send()
    {
        if ($this->getConversation()->getOrder()) {
            \XLite\Core\Mailer::sendOrderMessageNotifications($this);
        } else {
            \XLite\Core\Mailer::sendMessageNotifications($this);
        }

        return $this;
    }

    /**
     * Get IDs list for notifications resetting
     *
     * @return integer[]
     */
    protected function getNotificationProfileIds()
    {
        $list = array_filter(array_map(function ($member) {
            return $member->isAdmin()
                ? $member->getProfileId()
                : null;
        }, $this->getConversation()->getMembers()->toArray()));

        if (!$this->getAuthor()->isAdmin()) {
            $list[] = 0;
        }

        return $list;
    }

    /**
     * Open dispute
     *
     * @return boolean
     */
    public function openDispute()
    {
        $result = false;

        if ($this->getConversation()->getOrder() && !$this->getConversation()->getOrder()->getIsOpenedDispute()) {
            if (!$this->getBody()) {
                $this->setBody(static::t('Dispute opened by X', ['name' => $this->getAuthorName()]));
            }
            $this->setType(static::MESSAGE_TYPE_DISPUTE_OPEN);
            $this->getConversation()->getOrder()->setIsOpenedDispute(true);
            if (!$this->getAuthor()->isAdmin()) {
                \XLite\Core\TmpVars::getInstance()->XCVendorMessagesDisputesUpdateTimestamp = LC_START_TIME;
            }
            $result = true;
        }

        return $result;
    }

    /**
     * Close dispute
     *
     * @return boolean
     */
    public function closeDispute()
    {
        $result = false;

        if ($this->getConversation()->getOrder() && $this->getConversation()->getOrder()->getIsOpenedDispute()) {
            if (!$this->getBody()) {
                $this->setBody(static::t('Dispute closed by X', ['name' => $this->getAuthorName()]));
            }
            $this->setType(static::MESSAGE_TYPE_DISPUTE_CLOSE);
            $this->getConversation()->getOrder()->setIsOpenedDispute(false);
            $result = true;
        }

        return $result;
    }

    /**
     * Check if store administrator should receive email notification about new message
     *
     * @return boolean
     */
    public function isShouldSendToAdmin()
    {
        if (!$this->getAuthor()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)) {
            if ($this->getConversation()->getMembers()->count() <= 1) {
                return true;
            }

            if (in_array($this->getType(), [static::MESSAGE_TYPE_DISPUTE_OPEN, static::MESSAGE_TYPE_DISPUTE_CLOSE])) {
                return true;
            }

            if (
                \XLite\Module\XC\VendorMessages\Main::isMultivendor()
                && $this->getConversation()->getOrder()
                && $this->getConversation()->getOrder()->getIsOpenedDispute()
            ) {
                return true;
            }
        }

        return false;
    }
} 