<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Profile;


class CreatedAdmin extends AProfile
{
    public static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    public static function getDir()
    {
        return 'profile_created';
    }
}