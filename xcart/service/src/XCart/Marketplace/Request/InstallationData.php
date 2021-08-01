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

class InstallationData extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_INSTALLATION_DATA;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema(
            [
                Constant::FIELD_INSTALLATION_DATE => [
                    'filter' => FILTER_VALIDATE_INT,
                ],
            ]
        );
    }
}
