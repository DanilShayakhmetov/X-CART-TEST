<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;


use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;

interface DataSource
{
    /**
     * @param Data $data
     *
     * @return boolean
     */
    static public function isApplicable(Data $data);

    /**
     * @param Data $data
     *
     * @return static
     */
    static public function buildNew(Data $data);
}