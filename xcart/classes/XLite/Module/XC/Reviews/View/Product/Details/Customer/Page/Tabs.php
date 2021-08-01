<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Product\Details\Customer\Page;


class Tabs extends \XLite\View\Product\Details\Customer\Page\Tabs implements \XLite\Base\IDecorator
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params['anonymous'] = \XLite\Core\Auth::getInstance()->isLogged();

        return $params;
    }
}