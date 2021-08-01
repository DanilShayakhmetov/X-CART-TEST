<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ConfigParser;

class ConfigWrongFormattedFileException extends ConfigParserException
{
    /**
     * @param string $file
     *
     * @return self
     */
    public static function fromWrongFormattedFile($file)
    {
        return new self(sprintf('Unable to parse config file "%s" (probably it has a wrong format)', $file));
    }
}
