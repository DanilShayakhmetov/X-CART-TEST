<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View;

abstract class CommonResources extends \XLite\Module\XC\ThemeTweaker\View\CommonResources implements \XLite\Base\IDecorator
{
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (!(null === $adminZone ? \XLite::isAdminZone() : $adminZone)) {
            $list[static::RESOURCE_JS][] = 'js/bootstrap-tabcollapse.js';
            $list[static::RESOURCE_JS][] = 'js/jquery.collapser.js';
            $list[static::RESOURCE_JS][] = 'js/jquery.floating-label.js';
            $list[static::RESOURCE_JS][] = 'js/jquery.path.js';
            $list[static::RESOURCE_JS][] = 'js/jquery.fly.js';
            $list[static::RESOURCE_JS][] = 'js/utils.js';
            $list[static::RESOURCE_JS][] = 'js/header.js';
            $list[static::RESOURCE_JS][] = 'js/footer.js';

            if ($this->isCheckoutLayout()) {
                $list[static::RESOURCE_CSS][] = array(
                    'file'  => 'modules/XC/FastLaneCheckout/css/header.less',
                    'media' => 'screen',
                    'merge' => 'bootstrap/css/bootstrap.less',
                );
            }
        }

        return $list;
    }
}
