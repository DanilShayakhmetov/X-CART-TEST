<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Dashboard\Admin\InfoBlock\Alert;

use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Module\XC\VendorMessages\Model\Message;

/**
 * @ListChild (list="dashboard.info_block.alerts", weight="200", zone="admin")
 */
class Messages extends \XLite\View\Dashboard\Admin\InfoBlock\AAlert
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $result   = parent::getCSSFiles();
        $result[] = 'modules/XC/VendorMessages/alert.less';

        return $result;
    }

    /**
     * @return int
     */
    protected function getCounter()
    {
        return Database::getRepo(Message::class)->countUnread();
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' vendor-messages-messages';
    }

    /**
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('modules/XC/VendorMessages/images/mail.svg');
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Messages');
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
                'messages' => 'U',
            ]
        );
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
