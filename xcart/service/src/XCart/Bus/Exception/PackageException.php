<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

use RuntimeException;

class PackageException extends RuntimeException
{
    const ERR_BAD_ARGUMENTS = 400;
    const ERR_GENERIC       = 500;

    /**
     * @var array
     */
    private $params;

    /**
     * @return PackageException
     */
    public static function fromNoFile(): PackageException
    {
        return new self('exception.addon-package.missing-file-error', self::ERR_BAD_ARGUMENTS);
    }

    /**
     * @return PackageException
     */
    public static function fromNonPharArchive(): PackageException
    {
        return new self('exception.addon-package.wrong-file-error', self::ERR_BAD_ARGUMENTS);
    }

    /**
     * @return PackageException
     */
    public static function fromNonFormatArchive(): PackageException
    {
        $exception = new self('exception.addon-package.wrong-format-error', self::ERR_BAD_ARGUMENTS);
        $exception->setParams([]);

        return $exception;
    }

    /**
     * @param \Exception $previous
     *
     * @return PackageException
     */
    public static function fromGenericError($previous): PackageException
    {
        $exception = new self('exception.addon-package.generic-error', self::ERR_GENERIC);
        $exception->setParams(
            [
                [
                    $previous->getMessage(),
                    method_exists($previous, 'getParams') ? $previous->getParams() : []
                ]
            ]
        );

        return $exception;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
