<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Enum;

/**
 * Boolean enumrable (0 and 1)
 */
class Boolean extends \XLite\Core\Validator\Enum\AEnum
{
    /**
     * Constructor
     * 
     * @param array $list List of allowe values OPTIONAL
     *
     * @return void
     */
    public function __construct(array $list = array())
    {
        parent::__construct();

        $this->list[] = '1';
        $this->list[] = '0';
    }
}
