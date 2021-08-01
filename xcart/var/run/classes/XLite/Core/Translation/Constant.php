<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Translation;


class Constant extends \XLite\Core\Translation\Label
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function translate()
    {
        return $this->value;
    }
}