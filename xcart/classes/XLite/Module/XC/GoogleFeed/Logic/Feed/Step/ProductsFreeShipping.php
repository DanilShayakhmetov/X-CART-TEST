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
 * Products ste p
 *
 * @Decorator\Depend("XC\FreeShipping")
 */
class ProductsFreeShipping extends \XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\Product $model
     * @return array
     */
    protected function getProductRecord(\XLite\Model\Product $model)
    {
        $result = parent::getProductRecord($model);

        // Doesnt require shipping
        if ($model->isShipForFree()) {
            $result['g:shipping'] = $this->getShippingRecord($model);
        } elseif (
            !$model->getFreeShip()
            && 0 < $model->getFreightFixedFee()
        ) {
            $result['g:shipping'] = $this->getShippingRecord($model, $model->getFreightFixedFee());
        }

        return $result;
    }
}