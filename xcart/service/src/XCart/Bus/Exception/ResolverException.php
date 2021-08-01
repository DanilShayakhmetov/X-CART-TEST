<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

class ResolverException extends \RuntimeException
{
    /**
     * @param string $type
     *
     * @return ResolverException
     */
    public static function fromNotExpectedType($type)
    {
        return new self(sprintf('Resolver should be called inside %s or its inheritance', $type));
    }
}
