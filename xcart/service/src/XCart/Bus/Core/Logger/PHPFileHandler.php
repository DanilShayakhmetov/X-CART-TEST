<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Logger;

use Monolog\Handler\StreamHandler;

class PHPFileHandler extends StreamHandler
{
    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (!file_exists($this->url)) {
            parent::write(['formatted' => "<?php die(); ?>\n"]);
        }

        parent::write($record);
    }
}
