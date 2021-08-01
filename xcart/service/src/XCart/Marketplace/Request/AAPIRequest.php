<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use Psr\Log\LoggerInterface;

abstract class AAPIRequest extends ARequest
{
    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return null;
    }

    /**
     * @param LoggerInterface $logger
     * @param array           $params
     */
    public function logRequest(LoggerInterface $logger, $params): void
    {
        $logger->info(sprintf('Request to marketplace "%s"', static::class));
        $logger->debug('Request to marketplace', ['action' => static::class, 'params' => $params]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed           $data
     */
    public function logRawResponse(LoggerInterface $logger, $data): void
    {
        $logger->debug('Response from marketplace (raw)', ['action' => static::class, 'data' => $data]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed           $data
     */
    public function logParsedResponse(LoggerInterface $logger, $data): void
    {
        $logger->debug('Response from marketplace (parsed)', ['action' => static::class, 'data' => $data]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed           $data
     */
    public function logFormattedResponse(LoggerInterface $logger, $data): void
    {
        $logger->info(sprintf('Response from marketplace correctly received "%s"', static::class));
        $logger->debug('Response from marketplace (formatted)', ['action' => static::class, 'data' => $data]);
    }
}
