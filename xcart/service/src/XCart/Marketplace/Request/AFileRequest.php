<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use Psr\Log\LoggerInterface;

abstract class AFileRequest extends ARequest
{
    public function getAction(): ?string
    {
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getParams()
    {
        return null;
    }

    /**
     * @param LoggerInterface $logger
     * @param array           $params
     */
    public function logRequest(LoggerInterface $logger, $params): void
    {
        $logger->info(sprintf('Request to marketplace "%s"', $this->getFilePath()));
        $logger->debug('Request to marketplace', ['action' => $this->getFilePath(), 'params' => $params]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed $data
     */
    public function logRawResponse(LoggerInterface $logger, $data): void
    {
        $logger->debug('Response from marketplace (raw)', [$this->getFilePath(), 'data' => $data]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed $data
     */
    public function logParsedResponse(LoggerInterface $logger, $data): void
    {
        $logger->debug('Response from marketplace (parsed)', ['action' => $this->getFilePath(), 'data' => $data]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed $data
     */
    public function logFormattedResponse(LoggerInterface $logger, $data): void
    {
        $logger->info(sprintf('Response from marketplace correctly received "%s"', $this->getFilePath()));
        $logger->debug('Response from marketplace (formatted)', ['action' => $this->getFilePath(), 'data' => $data]);
    }
}
