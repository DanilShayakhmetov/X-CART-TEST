<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\ITransport;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Callback;

class AddonHashBatch extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_ADDON_HASH_BATCH;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Callback(function ($data) {
            return (bool) $data;
        });
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        return array_map(function ($item) {
            if (isset($item['result'])) {
                return json_decode($item['result'], true);
            }

            return null;
        }, (array) $data);
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            Constant::FIELD_MODULE_ID => [],
        ];
    }
}
