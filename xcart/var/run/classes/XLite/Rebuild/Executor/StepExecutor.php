<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild\Executor;


use Includes\Decorator\Utils\CacheManager;
use Includes\Decorator\Utils\PluginManager;

class StepExecutor implements IRebuildExecutor
{
    public function isApplicable($payloadData)
    {
        if (!isset($payloadData['step_name'])) {
            return false;
        }
        $name = $payloadData['step_name'];

        if (!PluginManager::getPlugins($name)) {
            return false;
        }
        return (boolean)$this->mapStep($name);
    }


    public function execute($payloadData, $rebuildId)
    {
        CacheManager::runStep($this->mapStep($payloadData['step_name']));

        return [
            'state' => CacheManager::$skipStepCompletion
                ? 'in_progress'
                : 'finished'
        ];
    }

    /**
     * @param $stepString
     *
     * @return mixed|null
     */
    protected function mapStep($stepString)
    {
        $map = [
            CacheManager::STEP_FIRST    => 'step_first',
            CacheManager::STEP_SECOND   => 'step_second',
            CacheManager::STEP_THIRD    => 'step_third',
            CacheManager::STEP_FOURTH   => 'step_fourth',
            CacheManager::STEP_FIFTH    => 'step_fifth',
            CacheManager::STEP_SIX      => 'step_six',
            CacheManager::STEP_SEVEN    => 'step_seven',
            // CacheManager::STEP_EIGHT    => 'step_eight',
            CacheManager::STEP_NINE     => 'step_nine',
            CacheManager::STEP_TEN      => 'step_ten',
            CacheManager::STEP_ELEVEN   => 'step_eleven',
            CacheManager::STEP_TWELVE   => 'step_twelve',
            CacheManager::STEP_THIRTEEN => 'step_thirteen',
        ];

        $key = array_search($stepString, $map, true);

        return $key && isset($map[$key])
            ? $key
            : null;
    }
}