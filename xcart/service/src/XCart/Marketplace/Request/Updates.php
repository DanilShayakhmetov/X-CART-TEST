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

class Updates extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_CHECK_FOR_UPDATES;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema(
            [
                Constant::FIELD_IS_UPGRADE_AVAILABLE  => \FILTER_VALIDATE_BOOLEAN,
                Constant::FIELD_ARE_UPDATES_AVAILABLE => \FILTER_VALIDATE_BOOLEAN,
            ]
        );
    }
}
