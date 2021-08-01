<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

class UpgradeDeciderException extends \Exception
{
    const INSTALLED_NOT_FOUND   = 1;
    const MARKETPLACE_NOT_FOUND = 2;
    const WRONG_VERSION_FORMAT  = 3;
}
