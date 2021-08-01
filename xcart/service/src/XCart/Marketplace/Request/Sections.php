<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\SchemaList;

class Sections extends AAPIRequest
{
    /**
     * @var array schema fields
     */
    public static $schema = [
        Constant::FIELD_SECTION_TYPE => [
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => ['regexp' => Constant::REGEXP_WORD],
        ],
        Constant::FIELD_SECTION_POS => FILTER_VALIDATE_INT,
        Constant::FIELD_SECTION_TAG => [
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => ['regexp' => Constant::REGEXP_WORD],
        ],
        Constant::FIELD_SECTION_IMAGE => FILTER_SANITIZE_URL,
        Constant::FIELD_SECTION_BANNER => FILTER_SANITIZE_URL,
        Constant::FIELD_SECTION_ADDON => [
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => ['regexp' => Constant::REGEXP_WORD],
        ],
        Constant::FIELD_SECTION_HTML => [
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => ['regexp' => Constant::REGEXP_WORD],
        ],
        Constant::FIELD_SECTION_CSS => [
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => ['regexp' => Constant::REGEXP_WORD],
        ],
        Constant::FIELD_SECTION_TRANSLATIONS => [
            'flags'   => FILTER_FORCE_ARRAY,
        ],
        Constant::FIELD_SECTION_ADDONS => [
            'flags'   => FILTER_FORCE_ARRAY,
        ],
    ];
    
    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_SECTIONS;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new SchemaList(static::$schema, false);
    }
}