<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Callback;

class Waves extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_WAVES;
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        $result = [];
        foreach ($data as $id => $name) {
            $result[] = [
                'id' => $id,
                'name' => $name
            ];
        }

        return $result;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Callback(
            function ($data) {
                foreach ((array) $data as $key => $value) {
                    if (!is_int($key) || !is_string($value)) {
                        return false;
                    }
                }

                return true;
            }
        );
    }
}
