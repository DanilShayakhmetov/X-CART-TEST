<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Logger;

use Monolog\Formatter\FormatterInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Guzzle extends ALogger
{
    /**
     * @return FormatterInterface
     */
    protected function getFormatter(): FormatterInterface
    {
        return new XCartFormatter();
    }
}
