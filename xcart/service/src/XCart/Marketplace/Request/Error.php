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

class Error extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return null;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema(
            [
                Constant::FIELD_ERROR_CODE    => [
                    'filter'  => \FILTER_VALIDATE_INT,
                    'options' => [],
                ],
                Constant::FIELD_ERROR_MESSAGE => [
                    'filter'  => \FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
            ]
        );
    }
}
