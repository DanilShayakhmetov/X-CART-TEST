<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use Psr\Log\LoggerInterface;
use XCart\Marketplace\Constant;
use XCart\Marketplace\IParser;
use XCart\Marketplace\ITransport;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\MarketplaceException;
use XCart\Marketplace\Parser\Callback as CallbackParser;
use XCart\Marketplace\Validator\Callback as CallbackValidator;

class CorePack extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_CORE_PACK;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new CallbackValidator(function ($data) {
            return (bool) $data;
        });
    }

    /**
     * @return IParser
     */
    public function getParser(): IParser
    {
        return new CallbackParser(function ($data) {
            return $data;
        });
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return array|mixed
     * @throws MarketplaceException
     */
    public function formatData($data, array $headers = [])
    {
        if (isset($headers['content-range'])) {
            if (!preg_match('#[a-z\s]+(\d+)-(\d+)/(\d+)#i', $headers['content-range'][0], $matches)) {
                throw new MarketplaceException('Unexpected content-range value: ' . $headers['content-range']);
            }

            return [
                'body'  => $data,
                'from'  => $matches[1],
                'to'    => $matches[2],
                'total' => $matches[3],
            ];
        }

        return $data;
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed           $data
     */
    public function logRawResponse(LoggerInterface $logger, $data): void
    {
        $data = is_array($data) ? $data['body'] : $data;

        parent::logRawResponse($logger, ['length' => \strlen($data)]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed           $data
     */
    public function logParsedResponse(LoggerInterface $logger, $data): void
    {
        $data = is_array($data) ? $data['body'] : $data;

        parent::logRawResponse($logger, ['length' => \strlen($data)]);
    }

    /**
     * @param LoggerInterface $logger
     * @param mixed           $data
     */
    public function logFormattedResponse(LoggerInterface $logger, $data): void
    {
        parent::logFormattedResponse(
            $logger,
            \is_array($data)
                ? (['data' => ['length' => \strlen($data['body'])]] + $data)
                : ['length' => \strlen($data)]
        );
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            Constant::FIELD_VERSION => [],
            Constant::FIELD_GZIPPED => true,
        ];
    }
}
