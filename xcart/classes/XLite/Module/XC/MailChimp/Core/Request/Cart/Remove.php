<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Cart;

use XLite\Module\XC\MailChimp\Core\Request\Campaign as MailChimpCampaign;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;

class Remove extends MailChimpRequest
{
    protected $campaignIdFromRequest;

    /**
     * @var string
     */
    protected $cartId;

    /**
     * @param string $cartId
     */
    public function __construct($cartId)
    {
        $campaignIdFromRequest = MailChimpCampaign\Get::getCampaignIdFromRequest();
        $this->cartId          = $cartId;

        parent::__construct('Removing cart', 'delete', '');
    }

    /**
     * @param string $cartId
     *
     * @return self
     */
    public static function getRequest($cartId): self
    {
        return new self($cartId);
    }

    /**
     * @param string $cartId
     *
     * @return mixed
     */
    public static function scheduleAction($cartId)
    {
        return self::getRequest($cartId)->schedule();
    }

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        foreach (array_keys(MailChimpStore\Get::getActiveStores($this->campaignIdFromRequest)) as $storeId) {
            Check::dropActionCache($storeId, $this->cartId);

            $this->setAction("ecommerce/stores/{$storeId}/carts/{$this->cartId}");

            parent::execute();
        }

        return null;
    }
}
