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
 * https://developer.paypal.com/docs/api/webhooks/#webhooks_get-all
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\Webhooks\Webhook[] webhooks
 */
class WebhooksList extends PayPalResourceModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Webhooks\Webhook[]
     */
    public function getWebhooks()
    {
        return $this->webhooks;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Webhooks\Webhook[] $webhooks
     *
     * @return WebhooksList
     */
    public function setWebhooks($webhooks)
    {
        $this->webhooks = $webhooks;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Webhooks\Webhook $webhook
     *
     * @return WebhooksList
     */
    public function addWebhook($webhook)
    {
        if (!$this->getWebhooks()) {

            return $this->setWebhooks([$webhook]);
        }

        return $this->setWebhooks(
            array_merge($this->getWebhooks(), [$webhook])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Webhooks\Webhook $webhook
     *
     * @return WebhooksList
     */
    public function removeWebhook($webhook)
    {
        return $this->setWebhooks(
            array_diff($this->getWebhooks(), [$webhook])
        );
    }

    /**
     * @param string         $anchorType Allowed values: APPLICATION, ACCOUNT
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return WebhooksList
     */
    public static function get($anchorType = 'APPLICATION', $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        $json = self::executeCall(
            '/v1/notifications/webhooks?anchor_type=' . $anchorType,
            'GET',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return (new self)->fromJson($json);
    }
}
