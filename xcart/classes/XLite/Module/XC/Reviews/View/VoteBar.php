<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View;

use XLite\Core\View\DynamicWidgetInterface;

/**
 * Vote bar widget
 *
 */
class VoteBar extends \XLite\View\VoteBar implements DynamicWidgetInterface
{
    /**
     * Return list of tooltips
     *
     * @return array
     */
    protected function getStarTooltips()
    {
        return [
            1 => static::t("star_tooltip_1"),
            2 => static::t("star_tooltip_2"),
            3 => static::t("star_tooltip_3"),
            4 => static::t("star_tooltip_4"),
            5 => static::t("star_tooltip_5"),
        ];
    }

    /**
     * Return star tooltip
     *
     * @param $num
     *
     * @return string
     */
    protected function getStarTooltip($num)
    {
        return isset($this->getStarTooltips()[$num])
            ? $this->getStarTooltips()[$num]
            : null;
    }
}
