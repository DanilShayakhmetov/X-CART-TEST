<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use XCart\Bus\Exception\MarketplaceException;
use XCart\Marketplace\Connector;
use XCart\Marketplace\IConnector;
use XCart\Marketplace\Logger;
use XCart\Marketplace\RangeIterator;
use XCart\Marketplace\Request\ARequest;
use XCart\Marketplace\Request\RequestException;
use XCart\Marketplace\Transport\AHTTP;
use XCart\Marketplace\Transport\TransportException;

class Marketplace
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var IConnector
     */
    protected $connector;

    public function __construct($config)
    {
        $this->logger = $this->initializeLogger($config['logger'] ?? []);

        $this->connector = new Connector([
            AHTTP::ENDPOINT => $config['endpoint'] ?? [],
            'logger'        => $this->logger,
            'common_params' => $config['common_params'] ?? [],
        ]);
    }

    /**
     * @param string $requestName
     * @param array  $params
     *
     * @return mixed|null
     * @throws MarketplaceException
     * @throws TransportException
     */
    public function getData($requestName, array $params = [])
    {
        try {
            $request = ARequest::getRequest($requestName, $params);

            $data = $this->connector->getData($request);
            if ($data === null) {
                // todo: check for logging
                throw MarketplaceException::fromInvalidResponse(
                    $requestName,
                    $this->connector->getLastResponse()
                );
            }

            return $data;

        } catch (RequestException $e) {
            $this->logger->debug('Wrong request name', [
                'requestName' => $requestName,
                'message'     => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * @param string $requestName
     * @param array  $params
     * @param array  $state
     *
     * @return RangeIterator
     */
    public function getRangeIterator($requestName, array $params = [], array $state = []): RangeIterator
    {
        return new RangeIterator(
            RangeIterator::getClosure([$this, 'getData'], $requestName, $params),
            $state
        );
    }

    /**
     * @param LoggerInterface|array $config
     *
     * @return LoggerInterface
     */
    private function initializeLogger($config): LoggerInterface
    {
        if ($config instanceof LoggerInterface) {
            return $config;
        }

        if (isset($config['writer'])) {
            return new Logger(
                [
                    'level'        => $config['level'] ?? LogLevel::INFO,
                    'writer'       => $config['writer'],
                    'backtrace'    => $config['backtrace'] ?? false,
                    'path_aliases' => $config['path_aliases'] ?? [],
                ]
            );
        }

        return new NullLogger();
    }
}
