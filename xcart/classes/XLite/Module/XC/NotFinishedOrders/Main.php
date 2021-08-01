<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Constants: Create NFO modes
     */
    const NFO_MODE_ON_FAILURE = 'onFailure';
    const NFO_MODE_ON_PLACE   = 'onPlaceOrder';

    protected static function moveTemplatesInLists()
    {
        return [
            'failed_transaction/parts/transaction_url.twig' => [
                static::TO_DELETE => [
                    ['failed_transaction.after'],
                ],
            ],
        ];
    }

    /**
     * Return true if NFO must be created on payment failure
     *
     * @return boolean
     */
    public static function isCreateOnFailure()
    {
        return static::NFO_MODE_ON_FAILURE == \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->create_nfo_mode;
    }

    /**
     * Return true if NFO must be created on place order
     *
     * @return boolean
     */
    public static function isCreateOnPlaceOrder()
    {
        return static::NFO_MODE_ON_PLACE == \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->create_nfo_mode;
    }
}
