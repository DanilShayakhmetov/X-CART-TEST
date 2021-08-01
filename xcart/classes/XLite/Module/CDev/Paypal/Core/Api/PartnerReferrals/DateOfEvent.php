<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-date_of_event
 *
 * @property string event_type
 * @property string event_date
 */
class DateOfEvent extends PayPalModel
{
    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->event_type;
    }

    /**
     * Valid Values: ["BIRTH", "ESTABLISHED", "INCORPORATION", "OPERATION"]
     *
     * @param string $event_type
     *
     * @return DateOfEvent
     */
    public function setEventType($event_type)
    {
        $this->event_type = $event_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventDate()
    {
        return $this->event_date;
    }

    /**
     * Use ISO 8601 standards.
     *
     * @param string $event_date
     *
     * @return DateOfEvent
     */
    public function setEventDate($event_date)
    {
        $this->event_date = $event_date;

        return $this;
    }
}
