<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\GraphQL\Exception;



class UnexpectedValue extends \XLite\Core\Exception
{
    private $errors;

    public function __construct($message = "", $code = 0, \Throwable $previous = null, $errors = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * Return Errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}