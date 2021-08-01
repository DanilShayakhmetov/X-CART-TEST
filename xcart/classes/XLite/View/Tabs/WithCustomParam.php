<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;


use XLite\Core\Request;

abstract class WithCustomParam extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string
     */
    abstract protected function defineUrlParam();

    /**
     * @return string
     */
    protected function getUrlParam()
    {
        return Request::getInstance()->{$this->defineUrlParam()};
    }

    protected function buildTabURL($param)
    {
        return $this->buildURL(\XLite::getController()->getTarget(), '', [
            $this->defineUrlParam() => $param
        ]);
    }

    protected function getCurrentTarget()
    {
        return $this->getUrlParam();
    }
}