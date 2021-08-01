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
 * @Decorator\Depend("XC\SystemFields")
 */
class ProductsUpcIsbn extends \XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\SystemFields\Model\Product $model
     * @return string
     */
    protected function getMpn(\XLite\Model\Product $model)
    {
        $result = parent::getMpn($model);

        if (!$result && $model->getMnfVendor()) {
            $result = $model->getMnfVendor();
        }

        return $result;
    }

    /**
     * @param \XLite\Module\XC\SystemFields\Model\Product $model
     * @return string
     */
    protected function getGtin(\XLite\Model\Product $model)
    {
        $result = parent::getGtin($model);

        if (!$result && $model->getUpcIsbn()) {
            $result = $model->getUpcIsbn();
        }

        return $result;
    }
}