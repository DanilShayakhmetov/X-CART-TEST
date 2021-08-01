<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\System;

use Symfony\Component\Finder\Finder as SymfonyFinder;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Finder extends SymfonyFinder implements FinderInterface
{
    /**
     * @return static|SymfonyFinder
     */
    public function build()
    {
        return static::create();
    }
}
