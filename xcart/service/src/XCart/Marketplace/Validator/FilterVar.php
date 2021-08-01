<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Validator;

use XCart\Marketplace\IValidator;

class FilterVar implements IValidator
{
    /**
     * @var  int
     */
    private $filter;

    /**
     * @var array
     */
    private $options;

    /**
     * @param int   $filter
     * @param array $options
     */
    public function __construct($filter = \FILTER_DEFAULT, array $options = [])
    {
        $this->filter  = $filter;
        $this->options = $options;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function isValid($data)
    {
        return (bool) filter_var($data, $this->filter, ['options' => $this->options]);
    }
}
