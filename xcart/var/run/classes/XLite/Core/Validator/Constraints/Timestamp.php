<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Timestamp extends Constraint
{
    const INVALID_TIME_STAMP = 'INVALID_TIME_STAMP_ERROR';
    public $message;
}
