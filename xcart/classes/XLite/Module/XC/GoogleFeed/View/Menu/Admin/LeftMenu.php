<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\Menu\Admin;

/**
 * Top menu widget
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->addRelatedTarget('google_feed', 'google_shopping_groups');
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $return = parent::defineItems();

        $target = 'google_shopping_groups';

        $return['sales_channels'][self::ITEM_CHILDREN]['google_feed'] = [
            self::ITEM_TITLE      => static::t('Google product feed'),
            self::ITEM_TARGET     => $target,
            self::ITEM_CLASS      => 'google-feed',
            self::ITEM_PERMISSION => 'manage catalog',
            self::ITEM_WEIGHT     => 300,
        ];

        return $return;
    }
}
