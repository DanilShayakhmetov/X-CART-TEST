<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

use XLite\Module\XC\MailChimp\Core;
use XLite\Module\XC\MailChimp\Core\MailChimp;
use XLite\Module\XC\MailChimp\Main;

class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Mailchimp store id
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $mailchimpStoreId = '';

    /**
     * Check order statuses
     *
     * @return boolean
     *
     * @PostPersist
     * @PostUpdate
     */
    public function checkStatuses()
    {
        $changed = parent::checkStatuses();

        if ($changed
            && Main::isMailChimpECommerceConfigured()
        ) {
            MailChimp::getInstance()->updateOrder($this);
        }

        return $changed;
    }

    /**
     * Check if the order needs to send ECommerce360 data
     *
     * @return boolean
     */
    protected function isECommerce360Order()
    {
        return $this->isECommerce360Cart();
    }

    /**
     * Check if the order needs to send ECommerce360 data
     *
     * @return boolean
     */
    protected function isECommerce360Cart()
    {
        $request = \XLite\Core\Request::getInstance();

        return isset($request->{Core\Request::MAILCHIMP_CAMPAIGN_ID})
            && !empty($request->{Core\Request::MAILCHIMP_CAMPAIGN_ID})
            && isset($request->{Core\Request::MAILCHIMP_USER_ID})
            && !empty($request->{Core\Request::MAILCHIMP_USER_ID});
    }

    /**
     * @return string
     */
    public function getMailchimpStoreId()
    {
        return $this->mailchimpStoreId;
    }

    /**
     * @param string $mailchimpStoreId
     *
     * @return \XLite\Model\Order
     */
    public function setMailchimpStoreId($mailchimpStoreId)
    {
        $this->mailchimpStoreId = $mailchimpStoreId;
        return $this;
    }
}
