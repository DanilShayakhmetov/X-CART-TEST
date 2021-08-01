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
use XCart\Marketplace\Validator\SchemaListList;

class CheckAddonKey extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_CHECK_ADDON_KEY;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        $validator = new SchemaListList([
            Constant::FIELD_AUTHOR   => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_NAME     => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_KEY_TYPE => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_NUMBER],
            ],
            Constant::FIELD_KEY_DATA => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['regexp' => '/.*/'],
            ],
            Constant::FIELD_KEY      => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
        ]);

        return new Callback(function ($data) use ($validator) {
            return empty($data) || $validator->isValid($data);
        });
    }
}
