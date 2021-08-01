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

class Editions extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_EDITIONS;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema([
            Constant::FIELD_EDITION_NAME           => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_EDITION_XB_ID          => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_EDITION_IS_CLOUD       => \FILTER_VALIDATE_BOOLEAN,
            Constant::FIELD_EDITION_DESCRIPTION    => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_EDITION_PRICE          => [
                'filter'  => FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_EDITION_AVAIL_FOR_SALE => \FILTER_VALIDATE_BOOLEAN,
            Constant::FIELD_EDITION_XCN_PLAN       => \FILTER_VALIDATE_INT,
        ]);
    }
}
