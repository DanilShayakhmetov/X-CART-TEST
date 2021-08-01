<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ConfigParser;

class ConfigMissingFileException extends ConfigParserException
{
    /**
     * @param string $file
     *
     * @return self
     */
    public static function fromMissingFile($file)
    {
        return new self(sprintf('Config file "%s" does not exist or is not readable', $file));
    }
}
