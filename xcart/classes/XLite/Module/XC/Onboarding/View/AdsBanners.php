<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

class AdsBanners extends \XLite\View\AdsBanners implements \XLite\Base\IDecorator
{
    protected function isVisible()
    {
        if (\XLite\Core\Request::getInstance()->target === 'onboarding_wizard') {
            return false;
        }

        return parent::isVisible();
    }
}