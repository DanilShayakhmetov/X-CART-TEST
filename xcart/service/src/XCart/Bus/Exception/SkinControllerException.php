<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

class SkinControllerException extends \RuntimeException
{
    const ERR_ARGUMENTS      = 400;
    const ERR_UNKNOWN_MODULE = 404;
    const ERR_ALREADY_ACTIVE = 200;
    const ERR_GENERIC        = 500;

    /**
     * @return SkinControllerException
     */
    public static function fromWrongArguments()
    {
        return new self(sprintf('Skin module id is required'), self::ERR_ARGUMENTS);
    }

    /**
     * @param string $id
     *
     * @return SkinControllerException
     */
    public static function fromUnknownModule($id)
    {
        return new self(sprintf('Cannot find a skin module (%s) to activate', $id), self::ERR_UNKNOWN_MODULE);
    }

    /**
     * @param string $id
     *
     * @return SkinControllerException
     */
    public static function fromAlreadyEnabled($id)
    {
        return new self(sprintf('The skin module %s is already active', $id), self::ERR_ALREADY_ACTIVE);
    }

    /**
     * @param string $message
     *
     * @return SkinControllerException
     */
    public static function fromGenericError($message)
    {
        return new self(
            sprintf('An error encountered during the scenario calculation: %s', $message),
            self::ERR_GENERIC
        );
    }
}
