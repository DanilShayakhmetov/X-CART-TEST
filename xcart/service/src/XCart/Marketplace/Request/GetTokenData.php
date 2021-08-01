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

class GetTokenData extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_TOKEN_DATA;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema(
            [
                Constant::FIELD_PURCHASE => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_PROLONGATION => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
            ], false
        );
    }
}
