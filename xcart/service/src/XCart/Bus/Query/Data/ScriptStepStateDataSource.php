<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ScriptStepStateDataSource extends SerializedDataSource
{
    /**
     * @param Application      $app
     * @param StorageInterface $storage
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        StorageInterface $storage
    ) {
        return new static(
            $storage->build($app['config']['cache_dir'], 'scriptStepStateStorage')
        );
    }
}