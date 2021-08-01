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

class Banners extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_ALL_BANNERS;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new SchemaList(
            [
                Constant::FIELD_BANNER_IMG     => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_BANNER_MODULE  => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_BANNER_URL     => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_BANNER_SECTION => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
            ]
        );
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        return array_map(function ($item) {
            return [
                'image'   => preg_replace('/^https?:/', '', $item['banner_img']),
                'module'  => $item['banner_module'],
                'url'     => html_entity_decode($item['banner_url']),
                'section' => $item['banner_section'],
            ];
        }, $data);
    }
}
