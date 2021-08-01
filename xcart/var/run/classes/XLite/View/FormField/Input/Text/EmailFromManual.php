<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;


use XLite\View\FormField\Select\EmailFrom;

class EmailFromManual extends \XLite\View\FormField\Input\Text\Email
{
    protected function isRequired()
    {
        $result = parent::isRequired();

        $request = \XLite\Core\Request::getInstance();

        if ($result && mb_strtolower($request->action) === 'update') {
            $result = $request->mail_from_type === EmailFrom::OPTION_MANUAL;
        }

        return $result;
    }

    protected function getDefaultPlaceholder()
    {
        return static::t('Specify email address');
    }
}