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

class Tags extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_ALL_TAGS;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new SchemaList(
            [
                Constant::FIELD_TAG_NAME                   => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_TAG_BANNER_EXPIRATION_DATE => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_TAG_BANNER_IMG             => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_TAG_MODULE_BANNER          => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_TAG_BANNER_URL             => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'category'                                 => [
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
        return array_map(static function ($item) {
            $image = ($item['tag_banner_url'] || $item['tag_module_banner']) ? $item['tag_banner_img'] : '';

            $translations = [];
            foreach ((array) static::getElement($item, Constant::FIELD_TRANSLATIONS) as $code => $fields) {
                $translations[] = [
                    'code'     => $code,
                    'tag_name' => $fields['tag_name'],
                ];
            }

            return [
                'image'        => preg_replace('/^https?:/', '', $image),
                'module'       => $item['tag_module_banner'],
                'expires'      => $item['tag_banner_expiration_date'],
                'url'          => $item['tag_banner_url'],
                'name'         => html_entity_decode($item['tag_name']),
                'category'     => $item['category'],
                'translations' => $translations,
            ];
        }, $data);
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            'lng' => 'all',
        ];
    }
}
