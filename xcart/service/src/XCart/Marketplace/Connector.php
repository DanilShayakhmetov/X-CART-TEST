<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use XCart\Bus\Exception\MarketplaceException;
use XCart\Marketplace\Parser\ParserException;
use XCart\Marketplace\Request\AFileRequest;
use XCart\Marketplace\Request\ARequest;
use XCart\Marketplace\Request\Error;
use XCart\Marketplace\Request\RequestException;
use XCart\Marketplace\Transport\AHTTP;
use XCart\Marketplace\Transport\Guzzle;
use XCart\Marketplace\Transport\TransportException;
use XCart\Marketplace\Validator\ValidatorException;

class Connector implements IConnector
{
    /**
     * @var ITransport
     */
    private $transport;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $commonParams;

    /**
     * @var IRequest
     */
    private $errorRequest;

    /**
     * @var array
     */
    private $lastResponse;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        if (isset($config[AHTTP::ENDPOINT])) {
            $this->transport = new Guzzle([
                AHTTP::ENDPOINT => $config[AHTTP::ENDPOINT],
            ]);
        }

        $this->logger       = $config['logger'] ?? new NullLogger();
        $this->commonParams = $config['common_params'] ?? [];

        try {
            $this->errorRequest = ARequest::getRequest(Error::class);
        } catch (RequestException $exception) {
        }
    }

    /**
     * @param IRequest $request
     *
     * @return mixed|null
     * @throws TransportException
     * @throws MarketplaceException
     */
    public function getData($request)
    {
        if ($this->transport === null) {
            $this->logger->debug('Transport is not defined');

            return null;
        }

        $response = $this->getDataFromTransport($request);

        $parsed = $this->getParsedData($response['body'], $request);

        $this->lastResponse = $parsed;

        if (!$this->isDataValid($parsed, $request)) {
            $this->logger->critical('Invalid data received', ['action' => $request->getAction(), 'data' => $parsed]);

            return null;
        }

        try {
            $error = $this->getParsedData($response['body'], $this->errorRequest);
            if ($this->isDataValid($error, $this->errorRequest)) {
                $this->lastResponse = $error;
                $this->logger->critical('Invalid data received', ['action' => $request->getAction(), 'data' => $parsed]);

                return null;
            }
        } catch (MarketplaceException $exception) {
        }

        $result = $request->formatData($parsed, $response['headers']);

        $request->logFormattedResponse($this->logger, $result);

        return $result;
    }

    /**
     * @return array
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @param IRequest $request
     *
     * @return mixed|null|string
     * @throws TransportException
     */
    private function getDataFromTransport(IRequest $request)
    {
        $fileRequest = $request instanceof AFileRequest;

        $action  = $fileRequest ? $request->getFilePath() : $request->getAction();
        $params  = $this->getParams($request->getParams());
        $headers = $request->getHeaders();

        $request->logRequest($this->logger, $params);

        try {
            if ($fileRequest) {
                $data = $this->transport->getFileContent($action);

            } else {
                $data = $this->transport->doAPIRequest(
                    $action,
                    $params,
                    $headers,
                    $request::getTransportTTL()
                );
            }
        } catch (TransportException $exception) {
            if ($request->ignoreTransportErrors()) {
                $data = [
                    'headers' => [],
                    'body'    => $request->getDefaultResponse(),
                ];
            } else {
                throw $exception;
            }
        }

        $request->logRawResponse($this->logger, $data);

        return $data;
    }

    /**
     * @param mixed    $data
     * @param IRequest $request
     *
     * @return mixed|null
     * @throws MarketplaceException
     */
    private function getParsedData($data, IRequest $request)
    {
        if ($data === null) {
            return $data;
        }

        try {
            $parsed = $request->getParser()->getParsed($data);

            $request->logParsedResponse($this->logger, $parsed);

            return $parsed;

        } catch (ParserException $e) {
            throw MarketplaceException::fromParsingError(\get_class($request), $e->getMessage(), $data);
        }
    }

    /**
     * @param mixed    $data
     * @param IRequest $request
     *
     * @return bool
     * @throws MarketplaceException
     */
    private function isDataValid($data, IRequest $request): ?bool
    {
        if (empty($data)) {
            throw MarketplaceException::fromEmptyResponse(\get_class($request));
        }

        try {
            return $request->getValidator()->isValid($data);

        } catch (ValidatorException $e) {
            throw MarketplaceException::fromValidationError(\get_class($request), $e->getMessage(), $data);
        }
    }

    /**
     * @param mixed $params
     *
     * @return mixed|array
     */
    private function getParams($params)
    {
        return \is_array($params) ? array_merge($this->commonParams, $params) : $params;
    }
}
