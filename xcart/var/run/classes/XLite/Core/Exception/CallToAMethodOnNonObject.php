<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Exception;


class CallToAMethodOnNonObject extends \XLite\Core\Exception
{
    private $nonObject;
    private $method;
    private $args;

    public function __construct($nonObject, $method, $args = [])
    {
        $this->nonObject = $nonObject;
        $this->method = $method;
        $this->args = $args;

        parent::__construct(sprintf(
            'Call to a member function %s() on %s',
            $method,
            gettype($nonObject)
        ));
    }

    /**
     * Return NonObject
     *
     * @return mixed
     */
    public function getNonObject()
    {
        return $this->nonObject;
    }

    /**
     * Return Method
     *
     * @return mixed
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