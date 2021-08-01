<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Logic\Feed\Step;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Router;

/**
 * Products step
 *
 * @Decorator\Depend("CDev\Sale")
 */
class ProductsSale extends \XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\CDev\Sale\Model\Product $model
     * @return string
     */
    protected function getPrice(\XLite\Model\Product $model)
    {
        if ($model->getParticipateSale()) {
            $currency = \XLite::getInstance()->getCurrency();
            $parts = $currency->formatParts($model->getDisplayPriceBeforeSale());
            unset($parts['prefix'], $parts['suffix'], $parts['sign']);
            $parts['code'] = ' ' . strtoupper($currency->getCode());

            return implode('', $parts);
        }

        return parent::getPrice($model);
    }

    /**
     * @param \XLite\Model\Product $model
     * @return array
     */
    protected function getProductRecord(\XLite\Model\Product $model)
    {
        $result = parent::getProductRecord($model);

        if ($model->getParticipateSale()) {
            $currency = \XLite::getInstance()->getCurrency();
            $parts = $currency->formatParts($model->getDisplayPrice());
            unset($parts['prefix'], $parts['suffix'], $parts['sign']);
            $parts['code'] = ' ' . strtoupper($currency->getCode());

            $result['g:sale_price'] = implode('', $parts);
        }

        return $result;
    }
}