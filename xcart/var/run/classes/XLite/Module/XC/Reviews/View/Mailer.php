<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View;


 class Mailer extends \XLite\Module\XC\ThemeTweaker\View\Mailer implements \XLite\Base\IDecorator
{
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/Reviews/vote_bar.less',
        ]);
    }
}