<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL\Http;


class Response
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var int
     */
    private $code;

    /**
     * @var array
     */
    private $headers;

    public function __construct($body, $code, array $headers)
    {
        $this->body = $body;
        $this->code = (int)$code;
        $this->headers = $headers;
    }

    /**
     * Return Body
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return Code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return Headers
     *
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return Headers
     *
     * @param string $name
     *
     * @return array|null
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name])
            ? $this->headers[$name]
            : null;
    }
}