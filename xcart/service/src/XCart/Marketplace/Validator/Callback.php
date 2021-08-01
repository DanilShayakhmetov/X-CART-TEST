<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Validator;

use XCart\Marketplace\IValidator;

class Callback implements IValidator
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct($callback)
    {
        $this->callback = is_callable($callback) ? $callback : null;
    }

    /**
     * @param mixed $data
     *
     * @return boolean
     * @throws ValidatorException
     */
    public function isValid($data)
    {
        if (!is_callable($this->callback)) {

            throw new ValidatorException('$this->callback is not valid callable object');
        }

        return (bool) call_user_func($this->callback, $data);
    }
}
