<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\Menu\Admin;

/**
 * Left menu widget
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['catalog'])) {
            $list['catalog'][static::ITEM_CHILDREN]['global_tabs'] = [
                static::ITEM_TITLE  => static::t('Product tabs'),
                static::ITEM_TARGET => 'global_tabs',
                static::ITEM_WEIGHT => 390,
                static::ITEM_PERMISSION => 'manage orders',
            ];
        }

        return $list;
    }

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->relatedTargets['global_tabs'])) {
            $this->relatedTargets['global_tabs'] = [];
        }

        $this->relatedTargets['global_tabs'][] = 'global_tab';
    }
}