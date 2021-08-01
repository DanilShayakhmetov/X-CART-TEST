<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Webhooks;

use PayPal\Common\PayPalResourceModel;
use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;

/**
 * https://developer.paypal.com/docs/api/webhooks/#event-type_list
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\Webhooks\EventType[] event_types
 */
class WebhookEventTypes extends PayPalResourceModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Webhooks\EventType[]
     */
    public function getEventTypes()
    {
        return $this->event_types;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Webhooks\EventType[] $event_types
     *
     * @return WebhookEventTypes
     */
    public function setEventTypes($event_types)
    {
        $this->event_types = $event_types;

        return $this;
    }

    /**
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return WebhookEventTypes
     */
    public static function get($apiContext = null, $restCall = null)
    {
        $payLoad = '';

        $json = self::executeCall(
            '/v1/notifications/webhooks-event-types',
            'GET',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return (new self)->fromJson($json);
    }

    /**
     * @param string         $webhookId  webhook id
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return WebhookEventTypes
     */
    public static function getSubscriptions($webhookId, $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        $json = self::executeCall(
            '/v1/notifications/webhooks/' . $webhookId . '/event-types',
            'GET',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return (new self)->fromJson($json);
    }
}
