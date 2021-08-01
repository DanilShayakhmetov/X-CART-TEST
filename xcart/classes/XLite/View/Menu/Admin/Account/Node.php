<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\Account;

/**
 * Quick menu node
 */
class Node extends \XLite\View\Menu\Admin\ANode
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'menu/account';
    }

    /**
     * Return list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'menu.' . $this->getParam(static::PARAM_LIST);
    }
}
