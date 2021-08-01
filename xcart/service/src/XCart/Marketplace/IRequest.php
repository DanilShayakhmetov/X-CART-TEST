<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace;

use Psr\Log\LoggerInterface;

interface IRequest
{
    /**
     * @param array $params
     */
    public function __construct(array $params = []);

    /**
     * @return int
     */
    public static function getTransportTTL(): int;

    /**
     * @return bool
     */
    public function ignoreTransportErrors(): bool;

    /**
     * @return mixed
     */
    public function getDefaultResponse();

    /**
     * @return string|null
     */
    public function getAction(): ?string;

    /**
     * @return string|null
     */
    public function getFilePath(): ?string;

    /**
     * @return mixed|null
     */
    public function getParams();

    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @return IParser
     */
    public function getParser(): IParser;

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator;

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = []);

    /**
     * @param LoggerInterface $logger
     * @param array           $params
     */
    public function logRequest(LoggerInterface $logger, $params): void;

    /**
     * @param LoggerInterface $logger
     * @param mixed $data
     */
    public function logRawResponse(LoggerInterface $logger, $data): void;

    /**
     * @param LoggerInterface $logger
     * @param mixed $data
     */
    public function logParsedResponse(LoggerInterface $logger, $data): void;

    /**
     * @param LoggerInterface $logger
     * @param mixed $data
     */
    public function logFormattedResponse(LoggerInterface $logger, $data): void;
}
