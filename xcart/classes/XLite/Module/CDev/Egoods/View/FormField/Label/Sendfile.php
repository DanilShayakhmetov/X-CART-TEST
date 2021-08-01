<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\FormField\Label;


class Sendfile extends \XLite\View\FormField\Label
{
    protected function getDir()
    {
        return 'modules/CDev/Egoods/settings';
    }

    protected function isVisible()
    {
        return parent::isVisible() && !\XLite\Core\ConfigParser::getOptions(['other', 'use_sendfile']);
    }

    protected function getFieldTemplate()
    {
        return 'sendfile.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/CDev/Egoods/settings/style.css'
        ]);
    }

    protected function getArticleUrl()
    {
        return static::t('https://kb.x-cart.com/setting_up_x-cart_5_environment/configuring_attachments_sending.html');
    }
}