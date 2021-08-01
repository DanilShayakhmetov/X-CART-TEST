<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Dashboard\Admin\InfoBlock\Alert;

use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Module\XC\VendorMessages\Main as VendorMessagesMain;
use XLite\Module\XC\VendorMessages\Model\Message;

/**
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class MessagesMultivendor extends \XLite\Module\XC\VendorMessages\View\Dashboard\Admin\InfoBlock\Alert\Messages implements \XLite\Base\IDecorator
{
    /**
     * @return int
     */
    protected function getCounter()
    {
        return Auth::getInstance()->isVendor()
            ? Database::getRepo(Message::class)->countUnreadForVendor()
            : Database::getRepo(Message::class)->countUnreadForAdmin();
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            || (Auth::getInstance()->isVendor() && VendorMessagesMain::isVendorAllowedToCommunicate());
    }
}
