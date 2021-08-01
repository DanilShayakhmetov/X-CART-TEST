<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Core\Mail;


class EgoodsLinkCustomer extends \XLite\Core\Mail\Order\ACustomer
{
    static function getDir()
    {
        return 'modules/CDev/Egoods/egoods_links';
    }
}