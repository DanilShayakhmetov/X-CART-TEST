<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Exception;


/**
 * MethodNotFound
 */
class MethodNotFound extends \XLite\Core\Exception
{
    private $class;
    private $method;
    private $args;

    /**
     * MethodNotFound constructor.
     *
     * @param string $message
     * @param string $class
     * @param string $method
     * @param array  $args
     */
    public function __construct($message = "", $class, $method, $args = [])
    {
        $this->class = $class;
        $this->method = $method;
        $this->args = $args;

        parent::__construct($message);
    }

    /**
     * Return Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Return Method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return Args
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }
}