<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Context
{
    public const ACCESS_MODE_FULL         = 0b11111111;
    public const ACCESS_MODE_READ         = 0b00000001;
    public const ACCESS_MODE_READ_LICENSE = 0b00000010;
    public const ACCESS_MODE_WRITE        = 0b00000100;

    /**
     * Data access mode
     *
     * @var int
     */
    public $mode;

    /**
     * @var string
     */
    public $languageCode;

    /**
     * @var string
     */
    public $adminEmail;
}
