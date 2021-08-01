<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

interface IStorage
{
    /**
     * @return array
     */
    public function fetch();

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data);
}
