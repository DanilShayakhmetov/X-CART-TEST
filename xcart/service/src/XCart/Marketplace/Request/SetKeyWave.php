<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IParser;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Parser\Callback;
use XCart\Marketplace\Parser\JSON;
use XCart\Marketplace\Validator\FilterVar;

class SetKeyWave extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_CHANGE_KEY_WAVE;
    }

    /**
     * @return IParser
     */
    public function getParser(): IParser
    {
        $parser = new JSON();

        return new Callback(function ($data) use ($parser) {
            $parsed = $parser->getParsed($data);

            return $parsed[0] ?? null;
        });
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new FilterVar(\FILTER_VALIDATE_REGEXP, ['regexp' => '/^ok$/']);
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        return $data === 'ok';
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            Constant::FIELD_KEYS => [],
            Constant::FIELD_WAVE => '',
        ];
    }
}
