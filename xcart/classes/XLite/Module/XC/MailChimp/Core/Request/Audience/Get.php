<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Audience;

use XLite\Module\XC\MailChimp\Core\Request\Campaign as MailChimpCampaign;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;

class Get extends MailChimpRequest
{
    /**
     * @param string $campaignId
     *
     * @return string|null
     */
    public static function getListIdByCampaignId($campaignId): ?string
    {
        $campaign = MailChimpCampaign\Get::executeAction($campaignId);

        if (!$campaign) {
            return null;
        }

        $list = isset($campaign['list_id'])
            ? $campaign
            : $campaign[0];

        return $list['list_id'] ?? null;
    }
}
