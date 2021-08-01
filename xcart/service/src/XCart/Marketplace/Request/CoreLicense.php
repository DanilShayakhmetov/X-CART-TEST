<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Schema;

class CoreLicense extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_CORE_LICENSE;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema(
            [
                Constant::FIELD_CORE_KEY_VALUE      => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_AUTHOR     => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_NAME       => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_EXPIRATION => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_DATA       => [
                    'filter' => \FILTER_REQUIRE_ARRAY,
                ],

                Constant::FIELD_CORE_KEY_EDITION_NAME => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_EXP_DATE     => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_PROLONG_KEY  => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_WAVE         => [
                    'filter' => FILTER_DEFAULT,
                ],
                Constant::FIELD_CORE_KEY_XB_PRODUCTID => [
                    'filter' => FILTER_DEFAULT,
                ],
            ]
        );
    }
}
