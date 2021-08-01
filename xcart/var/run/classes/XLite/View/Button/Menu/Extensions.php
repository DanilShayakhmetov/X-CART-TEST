<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Menu;

/**
 * Extensions action
 */
class Extensions extends \XLite\View\Button\APopupLink
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'left_menu/extensions/body.twig';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target' => 'hot_addons_list',
            'widget' => 'XLite\View\HotAddons',
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' extensions';
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();

        return array_merge(
            $list,
            ['title' => static::t('Add addons')]
        );
    }

    /**
     * @return bool
     */
    protected function isCacheAvailable()
    {
        return true;
    }
}
