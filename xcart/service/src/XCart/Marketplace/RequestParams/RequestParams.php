<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\RequestParams;

use XCart\Marketplace\IRequestParams;

class RequestParams implements IRequestParams
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $headers;

    /**
     * @param array $params
     * @param array $config
     * @param array $headers
     */
    public function __construct(array $params = [], array $config = [], array $headers = [])
    {
        $this->params  = $params;
        $this->config  = $config;
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        // @todo: find another way to do it
        if (isset($data['headers']) && is_array($data['headers'])) {
            $this->headers = $data['headers'];
            unset($data['headers']);
        }

        $this->params = array_merge($this->params, array_diff_key($data, $this->config));
        $this->config = array_merge($this->config, array_intersect_key($data, $this->config));
    }
}
