<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Action;

use XLite\Model\Product;
use XLite\Module\XC\MailChimp\Core\Request\Product as MailChimpProduct;
use XLite\Module\XC\MailChimp\Logic\DataMapper;
use XLite\Module\XC\MailChimp\Main;

class ProductUpdate implements IMailChimpAction
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function execute(): void
    {
        foreach (Main::getMainStores() as $store) {
            MailChimpProduct\Update::scheduleAction($store->getId(), DataMapper\Product::getDataByProduct($this->product));
        }
    }
}