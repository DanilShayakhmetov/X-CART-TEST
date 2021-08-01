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
use PayPal\Validation\ArgumentValidator;

/**
 * https://developer.paypal.com/docs/api/webhooks/#definition-webhook
 *
 * @property string                                                  id
 * @property string                                                  url
 * @property \XLite\Module\CDev\Paypal\Core\Api\Webhooks\EventType[] event_types
 */
class Webhook extends PayPalResourceModel
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Webhook
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Webhook
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

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
     * @return Webhook
     */
    public function setEventTypes($event_types)
    {
        $this->event_types = $event_types;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Webhooks\EventType $event_type
     *
     * @return Webhook
     */
    public function addEventType($event_type)
    {
        if (!$this->getEventTypes()) {

            return $this->setEventTypes([$event_type]);
        }

        return $this->setEventTypes(
            array_merge($this->getEventTypes(), [$event_type])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Webhooks\EventType $event_type
     *
     * @return Webhook
     */
    public function removeWebhook($event_type)
    {
        return $this->setEventTypes(
            array_diff($this->getEventTypes(), [$event_type])
        );
    }

    /**
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return Webhook
     */
    public function create($apiContext = null, $restCall = null)
    {
        ArgumentValidator::validate($this->getUrl(), 'Url');
        ArgumentValidator::validate($this->getEventTypes(), 'EventTypes');

        $payLoad = $this->toJSON();

        $json = self::executeCall(
            '/v1/notifications/webhooks',
            'POST',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return $this->fromJson($json);
    }

    /**
     * @param string         $webhookId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return Webhook
     */
    public static function get($webhookId, $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        $json = self::executeCall(
            '/v1/notifications/webhooks/' . $webhookId,
            'GET',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return (new self)->fromJson($json);
    }

    /**
     * @param string         $webhookId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return boolean
     */
    public static function delete($webhookId, $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        self::executeCall(
            '/v1/notifications/webhooks/' . $webhookId,
            'DELETE',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return true;
    }
}
