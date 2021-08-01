<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Parser;

use XCart\Marketplace\IParser;

class JSON implements IParser
{
    /**
     * @param string $data
     *
     * @return mixed
     * @throws ParserException
     */
    public function getParsed($data)
    {
        $result = json_decode($data, true);

        if ($result === null && json_last_error() !== \JSON_ERROR_NONE) {

            throw new ParserException(json_last_error_msg(), json_last_error());
        }

        return $result;
    }
}
