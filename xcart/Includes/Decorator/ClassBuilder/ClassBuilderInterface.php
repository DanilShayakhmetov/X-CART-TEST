<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder;

interface ClassBuilderInterface
{
    public function buildClassname($class);

    public function buildPathname($pathname);

    public function getDecoratedAncestorForPathname($pathname);

    public function getDecoratedAncestorForClassname($class);
}