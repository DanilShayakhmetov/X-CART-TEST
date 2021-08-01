<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\Model\Product;

/**
 * GlobalTab
 */
class GlobalTab extends \XLite\Module\XC\CustomProductTabs\View\Model\GlobalTab
{
    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return (integer)\XLite\Core\Request::getInstance()->global_tab_id;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\CustomProductTabs\View\Form\Model\Product\GlobalTab';
    }
}