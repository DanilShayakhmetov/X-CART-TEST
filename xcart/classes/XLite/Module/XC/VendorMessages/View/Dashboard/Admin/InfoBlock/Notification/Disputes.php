<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Dashboard\Admin\InfoBlock\Notification;

use XLite\Core\Auth;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Module\XC\VendorMessages\Main as VendorMessagesMain;
use XLite\Module\XC\VendorMessages\Model\Message;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="200", zone="admin")
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class Disputes extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    use ExecuteCachedTrait;

    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'XCVendorMessagesDisputes';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' xc-vendormessages-disputes';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Disputes');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL(
            'messages',
            '',
            [
                'messages' => 'D',
            ]
        );
    }

    /**
     * Get entries count
     *
     * @return integer
     */
    protected function getCounter()
    {
        return $this->executeCachedRuntime(static function () {
            return Database::getRepo(Message::class)->countDisputes();
        });
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && VendorMessagesMain::isAllowDisputes()
            && $this->getCounter();
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && (Auth::getInstance()->hasRootAccess()
                || Auth::getInstance()->isPermissionAllowed('manage conversations'));
    }
}
