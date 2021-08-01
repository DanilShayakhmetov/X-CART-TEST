<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Button\Dropdown;


class MarkAs extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            [
                'class' => 'XLite\View\Button\Regular',
                'params'   => [
                    'label'      => 'Read',
                    'style'      => 'always-enabled action link list-action',
                    'action'     => 'mark_conversations_read',
                ],
                'position' => 100,
            ],
            [
                'class' => 'XLite\View\Button\Regular',
                'params'   => [
                    'label'      => 'Unread',
                    'style'      => 'always-enabled action link list-action',
                    'action'     => 'mark_conversations_unread',
                ],
                'position' => 200,
            ],
        ];
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' mark-as';
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/VendorMessages/button/mark_messages.js'
        ]);
    }
}