<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Parser;

use XCart\Marketplace\IParser;

class Callback implements IParser
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
     * @param string $data
     *
     * @return mixed
     * @throws ParserException
     */
    public function getParsed($data)
    {
        if (!is_callable($this->callback)) {

            throw new ParserException('$this->callback is not valid callable object');
        }

        return call_user_func($this->callback, $data);
    }
}
