<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\Product\Details\Customer;

use XLite\Core\Session;

/**
 * Next previous product widget
 *
 * @Decorator\Depend({"XC\NextPreviousProduct"})
 */
class NextPreviousProduct extends \XLite\Module\XC\NextPreviousProduct\View\Product\Details\Customer\NextPreviousProduct implements \XLite\Base\IDecorator
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $sessionCell = Session::getInstance()->{$this->getSessionCellName()};

        if (empty($sessionCell['items_list'])) {
            return $list;
        }

        if (method_exists($this, 'getCookieParameters')) {
            $cookieParams = $this->getCookieParameters();

            $categoryId = isset($cookieParams['categoryId']) ? $cookieParams['categoryId'] : '';

        } elseif (method_exists($this, 'getCategoryId')) {
            $categoryId = $this->getCategoryId();

        } else {
            $categoryId = '';
        }

        $cellName = $sessionCell['items_list']::getSessionCellName() . $categoryId;

        if (Session::getInstance()->{$cellName}) {
            $list[] = serialize(Session::getInstance()->{$cellName});
        }

        return $list;
    }
}
