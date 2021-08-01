<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL\Http\Client;


use XLite\Core\GraphQL\Http\IClient;

class Curl implements IClient
{
    private $config;

    public function __construct($endpoint, array $config = [])
    {
        $this->config = $config;
        $this->config[static::OPTION_ENDPOINT] = $endpoint;
    }


    public function request($method, array $options = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getOption(static::OPTION_ENDPOINT, $options));
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, $method === 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getOption(static::OPTION_POST_FIELDS, $options));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getOption(static::OPTION_HEADERS, $options) ?: []);

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return new \XLite\Core\GraphQL\Http\Response(
            $this->prepareBody($response),
            $httpCode,
            $this->prepareHeaders($response)
        );
    }

    protected function getOption($option, $options)
    {
        return isset($options[$option])
            ? $options[$option]
            : $this->getConfig($option);
    }

    public function getConfig($option = null)
    {
        return is_null($option)
            ? $this->config
            : (isset($this->config[$option]) ? $this->config[$option] : null);
    }

    protected function detectHeadersEnd($parts)
    {
        foreach ($parts as $k => $part) {
            if ($part === '') {
                if (isset($parts[$k + 1]) && strpos($parts[$k + 1], 'HTTP/') === 0) {
                    continue;
                }

                return $k;
            }
        }

        return false;
    }

    protected function prepareHeaders($response)
    {
        $parts = array_map('trim', explode("\n", $response));

        if (($i = $this->detectHeadersEnd($parts)) !== false) {
            $result = [];
            $headerLines = array_slice($parts, 0, $i + 1);

            foreach ($headerLines as $line) {
                $divPos = strpos($line, ':');

                if ($divPos !== false) {
                    $name = trim(substr($line, 0, $divPos));
                    $value = trim(substr($line, $divPos + 1));

                    $result[$name][] = $value;
                }
            }

            return $result;
        }

        return [];
    }

    protected function prepareBody($response)
    {
        $parts = array_map('trim', explode("\n", $response));

        if (($i = $this->detectHeadersEnd($parts)) !== false) {
            return implode("\n", array_slice($parts, $i + 1));
        }

        return $response;
    }
}