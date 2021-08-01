<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Logger;

use Monolog\Formatter\FormatterInterface;
use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Query\Data\ScenarioDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Rebuild extends ALogger
{
    private static $scenarioDataSource;

    /**
     * @param Application $app
     *
     * @return LoggerInterface
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app
    ) {
        static::$scenarioDataSource = $app[ScenarioDataSource::class];

        return parent::serviceConstructor($app);
    }

    /**
     * @return FormatterInterface
     */
    protected function getFormatter(): FormatterInterface
    {
        return new RebuildFormatter(static::$scenarioDataSource);
    }
}
