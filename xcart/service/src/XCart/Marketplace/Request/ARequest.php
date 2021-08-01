<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\IParser;
use XCart\Marketplace\IRequest;
use XCart\Marketplace\IRequestParams;
use XCart\Marketplace\ITransport;
use XCart\Marketplace\Parser\JSON;
use XCart\Marketplace\RequestParams\RequestParams;

abstract class ARequest implements IRequest
{
    /**
     * @var IRequestParams
     */
    protected $params;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = new RequestParams($this->getDefaultParams(), $this->getDefaultConfig());
        $this->params->setData($params);
    }

    /**
     * @return int
     */
    public static function getTransportTTL(): int
    {
        return ITransport::TTL_LONG;
    }

    /**
     * @param array|mixed $array
     * @param string      $k,...
     *
     * @return mixed|null
     */
    public static function getElement($array, $k)
    {
        $keys = array_slice(func_get_args(), 1);
        $key  = array_shift($keys);

        if (isset($array[$key])) {
            return $keys
                ? static::getElement(...array_merge([$array[$key]], $keys))
                : $array[$key];
        }

        return null;
    }

    /**
     * @param string $requestName
     * @param array  $params
     *
     * @return IRequest
     * @throws RequestException
     */
    public static function getRequest($requestName, array $params = []): IRequest
    {
        $requestClass = static::getRequestClass($requestName);

        return new $requestClass($params);
    }

    /**
     * @param string $requestName
     *
     * @return string
     * @throws RequestException
     */
    public static function getRequestClass($requestName): string
    {
        $className = $requestName;
        if (class_exists($className) && is_subclass_of($className, IRequest::class)) {
            return $className;
        }

        $className = 'XCart\Marketplace\Request\\' . $requestName;
        if (class_exists($className) && is_subclass_of($className, IRequest::class)) {
            return $className;
        }

        throw new RequestException(sprintf('%s does not defined', $requestName));
    }

    /**
     * @return mixed|null
     */
    public function getParams()
    {
        return $this->params->getParams();
    }

    /**
     * @return bool
     */
    public function ignoreTransportErrors(): bool
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getDefaultResponse()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->params->getHeaders();
    }

    /**
     * @return IParser
     */
    public function getParser(): IParser
    {
        return new JSON();
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        return $data;
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getDefaultConfig(): array
    {
        return [];
    }
}
