<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail;


interface IMail
{
    /**
     * @return string
     */
    static function getInterface();

    /**
     * @return string
     */
    static function getDir();
}