<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

use Symfony\Component\HttpFoundation\File\Exception\UploadException as SymfonyUploadException;

class UploadException extends SymfonyUploadException
{
    const ERR_BAD_ARGUMENTS = 400;
    const ERR_GENERIC       = 500;

    /**
     * @var array
     */
    private $params;

    /**
     * @return UploadException
     */
    public static function fromNoInput(): UploadException
    {
        return new self('exception.upload-addon.input-error', static::ERR_BAD_ARGUMENTS);
    }

    /**
     * @param string $basename
     *
     * @return UploadException
     */
    public static function fromRestrictedType($basename): UploadException
    {
        $exception = new self('exception.upload-addon.restriction-error', static::ERR_BAD_ARGUMENTS);
        $exception->setParams([$basename]);

        return $exception;
    }

    /**
     * @return UploadException
     */
    public static function fromNoOutput(): UploadException
    {
        return new self('exception.upload-addon.output-error', static::ERR_GENERIC);
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

