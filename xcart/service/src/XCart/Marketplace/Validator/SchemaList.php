<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Validator;

class SchemaList extends Schema
{
    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function isValid($data)
    {
        if (!is_array($data)) {

            return false;
        }

        foreach ((array) $data as $item) {
            if (!is_array($item) || !parent::isValid($item)) {

                return false;
            }
        }

        return true;
    }
}
