<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Domain\Module;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Data\Filter\AModifier;
use XCart\Bus\Query\Data\MarketplaceShopAdapter;

class PurchaseUrl extends AModifier
{
    /**
     * @var MarketplaceShopAdapter
     */
    private $marketplaceShopAdapter;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    public function __construct(Iterator $iterator, $field, $data, MarketplaceShopAdapter $marketplaceShopAdapter, UrlBuilder $urlBuilder)
    {
        parent::__construct($iterator, $field, $data);

        $this->marketplaceShopAdapter = $marketplaceShopAdapter;
        $this->urlBuilder             = $urlBuilder;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        $marketplaceShop   = $this->marketplaceShopAdapter->get();
        $xbProductId       = $item->xbProductId;
        $item->purchaseUrl = $marketplaceShop->getBuyNowURL(
            $xbProductId,
            $this->urlBuilder->buildServiceMainUrl("afterPurchase/{$item->author}/{$item->name}")
        );

        if (empty($item->purchaseUrl)) {
            $item->purchaseUrl = $marketplaceShop->getPurchaseURL($xbProductId);
        }

        return $item;
    }
}
