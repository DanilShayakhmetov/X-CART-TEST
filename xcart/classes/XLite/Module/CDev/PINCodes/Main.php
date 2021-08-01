<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Removes some inventory tracking templates to extend them with PIN Codes specific features
     *
     * @return array
     */
    protected static function moveTemplatesInLists()
    {
        return [
            'product/inventory/inv_track_amount.twig'   => [
                static::TO_DELETE => [
                    ['product.inventory.parts', \XLite\Model\ViewList::INTERFACE_ADMIN],
                ],
            ],
            'product/inventory/inv_track_selector.twig' => [
                static::TO_DELETE => [
                    ['product.inventory.parts', \XLite\Model\ViewList::INTERFACE_ADMIN],
                ],
            ],
        ];
    }
}
