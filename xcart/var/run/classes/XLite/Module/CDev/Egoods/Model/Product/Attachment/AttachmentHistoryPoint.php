<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\Product\Attachment;

/**
 * Product attachment history point
 *
 * @Entity
 * @Table  (name="product_attachment_history_points",
 *      indexes={
 *          @Index (name="attachment", columns={"attachment_id"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class AttachmentHistoryPoint extends \XLite\Model\AEntity
{
    // {{{ Collumns

    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Login
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $login;

    /**
     * Ip
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $ip;

    /**
     * Create / modify date (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $date = 0;

    /**
     * File path
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $path = '';

    /**
     * Additional details
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $additionalDetails = '';

    // }}}

    // {{{ Associations

    /**
     * Relation to a attachment
     *
     * @var \XLite\Module\CDev\FileAttachments\Model\Product\Attachment
     *
     * @ManyToOne  (targetEntity="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", inversedBy="history")
     * @JoinColumn (name="attachment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attachment;

    /**
     * Relation to a attachment
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Relation to a profile entity
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne  (targetEntity="XLite\Model\Profile")
     * @JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="SET NULL")
     */
    protected $profile;

    // }}}

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
     * Return Login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set Login
     *
     * @param string $login
     *
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Return Ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set Ip
     *
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
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
     * Return Attachment
     *
     * @return \XLite\Module\CDev\FileAttachments\Model\Product\Attachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Set Attachment
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment
     *
     * @return $this
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * Return Profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set Profile
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return $this
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Prepare order before save data operation
     *
     * @return void
     *
     * @PrePersist
     * @PreUpdate
     */
    public function prepareBeforeSave()
    {
        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Return Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set Path
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Return AdditionalDetails
     *
     * @return array
     */
    public function getAdditionalDetails()
    {
        $details = @unserialize($this->additionalDetails);
        return is_array($details) ? $details : [];
    }

    /**
     * Set AdditionalDetails
     *
     * @param array $additionalDetails
     *
     * @return $this
     */
    public function setAdditionalDetails($additionalDetails)
    {
        $this->additionalDetails = serialize($additionalDetails);
        return $this;
    }

    /**
     * Fill additional details
     */
    public function fillAdditionalDetails()
    {
        $details = [];

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $details['Referrer'] = $_SERVER['HTTP_REFERER'];
        }

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $details['User agent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        $this->setAdditionalDetails($details);
    }

    /**
     * Returns array of entity additional info (full path, user agent, etc)
     *
     * @return array
     */
    public function getAdditionalInfo()
    {
        $info = $this->getAdditionalDetails();
        if ($this->getPath()) {
            $info['Path'] = $this->getPath();
        }

        return $info;
    }
}