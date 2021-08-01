<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\AEventHook;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "16000")
 * @RebuildStep(script = "install", weight = "4000")
 */
class EnableHook extends AEventHook
{
    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'enable';
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return string|null
     */
    protected function getCacheId(ScriptState $scriptState): ?string
    {
        $parentStepState = $scriptState->getCompletedStepState(UpdateModulesList::class);

        return $parentStepState ? $parentStepState->data['cacheId'] : null;
    }

    /**
     * @param array[] $transitions
     *
     * @return array[]
     */
    protected function filterScriptTransitions(array $transitions): array
    {
        return array_filter($transitions, static function ($transition) {
            return in_array(
                $transition['transition'],
                [
                    ChangeUnitProcessor::TRANSITION_ENABLE,
                    ChangeUnitProcessor::TRANSITION_INSTALL_ENABLED,
                ],
                true
            );
        });
    }
}
