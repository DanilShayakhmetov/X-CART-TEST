<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ScriptStateDataSource extends SerializedSeparatedDataSource
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
            $app['config']['cache_dir'],
            'scriptStateStorage',
            $storage
        );
    }

    /**
     * @return int
     */
    protected function getItemTTL(): int
    {
        return 259200; // 3 day;
    }

    /**
     * @return string
     */
    protected function getOldDataFileName(): string
    {
        return 'scriptStateStorage';
    }

    /**
     * @return array
     */
    public function getRunning()
    {
        $states = $this->getAll();

        $runningStates = [
            ScriptState::STATE_INITIALIZED,
            ScriptState::STATE_IN_PROGRESS,
            ScriptState::STATE_ERROR_ABORTED,
        ];

        $running = array_filter($states, function (ScriptState $state) use ($runningStates) {
            return in_array($state->state, $runningStates, true);
        });

        return $running;
    }
}
