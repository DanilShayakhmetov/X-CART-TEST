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

class AddonsSearch extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_ADDONS_SEARCH;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema([
            Constant::FIELD_PRODUCTS           => [
                'filter'  => FILTER_CALLBACK,
                'options' => function ($item) {
                    return $item;
                },
            ],
            Constant::FIELD_NUM_FOUND_PRODUCTS => \FILTER_VALIDATE_INT,
            Constant::FIELD_RESULTS_FOR        => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
        ], false);
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            Constant::FIELD_SUBSTRING => '',
        ];
    }
}
