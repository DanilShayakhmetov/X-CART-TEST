<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Controller\Customer;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Module\QSL\CloudSearch\Controller\ApiControllerTrait;
use XLite\View\AView;

/**
 * CloudSearch API controller
 */
class CloudSearchApi extends \XLite\Controller\Customer\ACustomer
{
    use ApiControllerTrait;

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->params[] = 'action';
        $this->params[] = 'start';
        $this->params[] = 'limit';
        $this->params[] = 'ids';
    }

    protected function doActionGetPrices()
    {
        $prices = [];

        $currency = $this->getCart()->getCurrency();

        foreach ($this->getProducts() as $product) {
            $prices[$product->getProductId()] = AView::formatPrice($product->getDisplayPrice(), $currency);
        }

        $this->printJSONAndExit($prices);
    }

    protected function getProducts()
    {
        return Database::getRepo('XLite\Model\Product')->findByIds($this->getProductIds());
    }

    protected function getProductIds()
    {
        return explode(',', Request::getInstance()->ids);
    }

    /**
     * Stub for the CMS connectors
     *
     * @return boolean
     */
    protected function checkStorefrontAccessibility()
    {
        return true;
    }
}
