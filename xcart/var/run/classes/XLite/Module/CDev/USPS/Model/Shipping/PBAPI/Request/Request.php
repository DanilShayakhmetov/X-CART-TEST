<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request;

use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\ILogger;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\IRequest;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Logger;

class Request implements IRequest
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $verb;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $inputData;

    /**
     * @var ILogger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $communication = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param array[] $data
     *
     * @return array
     */
    protected static function parseErrors($data)
    {
        $errors = [];

        if (isset($data['errors'])) {
            foreach ($data['errors'] as $error) {
                $errors[] = [
                    'code'    => $error['errorCode'],
                    'message' => $error['errorDescription'],
                ];
            }
        } elseif (isset($data['fault'])) {
            $errors[] = [
                'code'    => '',
                'message' => $data['fault']['faultstring'],
            ];
        } else {
            foreach ($data as $error) {
                if (isset($error['errorCode'])) {
                    $errors[] = [
                        'code'    => $error['errorCode'],
                        'message' => $error['message'],
                    ];
                } elseif (isset($error['key'])) {
                    $errors[] = [
                        'code'    => $error['key'],
                        'message' => $error['message'],
                    ];
                }
            }
        }

        return $errors;
    }

    /**
     * @param string|null $url
     * @param string|null $verb
     * @param array|null  $inputData
     * @param array|null  $headers
     */
    public function __construct($url = null, $verb = null, $inputData = null, $headers = null)
    {
        if ($url !== null) {
            $this->setUrl($url);
        }

        if ($verb !== null) {
            $this->setVerb($verb);
        }

        if ($inputData !== null) {
            $this->setInputData($inputData);
        }

        if ($headers !== null) {
            $this->setHeaders($headers);
        }

        $this->logger = new Logger();
    }

    /**
     * @return array
     * @throws RequestException
     */
    public function performRequest()
    {
        $request       = new \XLite\Core\HTTP\Request($this->getUrl());
        $request->verb = $this->getVerb();

        $inputData = $this->getInputData();
        $request->body = is_string($inputData) ? $inputData : json_encode($inputData);

        foreach ($this->getHeaders() as $name => $value) {
            $request->setHeader($name, $value);
        }

        $this->logRequest();

        $response = $request->sendRequest();

        $this->logResponse($response);

        $body = [];
        if ($response->body) {
            $body = json_decode($response->body, true);
        }

        if ($response->code === 200 || $response->code === 201) {

            return $body;
        }

        if ($body) {
            $this->errors = static::parseErrors($body);
            $errorMessage = [];
            foreach ($this->errors as $error) {
                $errorMessage[] = ($error['code'] ? ($error['code'] . ': ') : '') . $error['message'];
            }

            throw new RequestException(implode(';', $errorMessage));
        }

        throw new RequestException(($response ? $response->code : 0) . ': ' . $request->getErrorMessage());
    }

    protected function logRequest()
    {
        $data = [
            get_class($this),
            'request',
            $this->getUrl(),
            $this->getVerb(),
            $this->getHeaders(),
            $this->getInputData(),
        ];

        $this->communication[] = $data;
        $this->logger->log($data);
    }

    /**
     * @param \PEAR2\HTTP\Request\Response $response
     */
    protected function logResponse(\PEAR2\HTTP\Request\Response $response)
    {
        $data = [
            get_class($this),
            'response',
            $response->code,
            $response->body,
        ];

        $this->communication[] = $data;
        $this->logger->log($data);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return static
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param string $verb
     *
     * @return static
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return static
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputData()
    {
        return $this->inputData;
    }

    /**
     * @param array $inputData
     *
     * @return static
     */
    public function setInputData($inputData)
    {
        $this->inputData = $inputData;

        return $this;
    }

    /**
     * @return ILogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param ILogger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getCommunication()
    {
        return $this->communication;
    }

    /**
     * @param array $communication
     */
    public function setCommunication($communication)
    {
        $this->communication = $communication;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}
