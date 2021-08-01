<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Campaign;

use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Request;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Get extends MailChimpRequest
{
    /**
     * @param string $campaignId
     */
    public function __construct($campaignId)
    {
        parent::__construct('Getting campaign', 'get', "campaigns/{$campaignId}");
    }

    /**
     * @param string $campaignId
     *
     * @return self
     */
    public static function getRequest($campaignId): self
    {
        return new self($campaignId);
    }

    /**
     * @param string $campaignId
     *
     * @return mixed
     */
    public static function executeAction($campaignId)
    {
        return ExecuteCached::executeCached(
            static function () use ($campaignId) {
                $result = self::getRequest($campaignId)->execute();

                return $result['recipients'] ?? null;
            },
            [__METHOD__, $campaignId]
        );
    }

    /**
     * @return string|null
     */
    public static function getCampaignIdFromRequest(): ?string
    {
        /** @var \XLite\Core\Request|\XLite\Module\XC\MailChimp\Core\Request $request */
        $request = Request::getInstance();

        return $request->{$request::MAILCHIMP_CAMPAIGN_ID} ?? null;
    }
}
