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

/**
 * todo: check negative response (@svowl)
 */
class Landing extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_LANDING_AVAILABLE;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema(
            [
                Constant::FIELD_LANDING => [
                    'filter'  => FILTER_VALIDATE_INT,
                    'options' => ['min_range' => 0],
                ],
            ]
        );
    }
}
