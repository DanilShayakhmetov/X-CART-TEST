<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\PreloadedLabels;


use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

 class Registrar extends \XLite\Core\PreloadedLabels\RegistrarAbstract implements \XLite\Base\IDecorator
{
    public function register(array $data)
    {
        if (ThemeTweaker::getInstance()->isInLabelsMode()) {
            $data = array_map(function($item) {
                return (string) $item;
            }, $data);
        }

        parent::register($data);
    }
}
