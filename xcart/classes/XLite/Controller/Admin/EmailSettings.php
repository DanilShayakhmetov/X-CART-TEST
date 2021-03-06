<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Email settings
 */
class EmailSettings extends \XLite\Controller\Admin\Settings
{
    public $page = self::EMAIL_PAGE;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Email Notifications');
    }

    /**
     * @return bool
     */
    public function isQueuesNoteVisible()
    {
        return !\XLite\Core\ConfigParser::getOptions(['queue', 'backgroundJobsSchedulingEnabled']);
    }
}
