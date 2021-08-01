<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Rollback;

use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\AXCartStep;
use XCart\Bus\Rebuild\Executor\Step\Execute\XCartStep as ExecuteXCartStep;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "rollback", weight = "10000")
 */
class XCartStepFinalize extends AXCartStep
{
    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getTransitions(ScriptState $scriptState): array
    {
        if (!$scriptState->parentState->isStepCompleted(ExecuteXCartStep::class)
            && $scriptState->parentState->stepState->id !== ExecuteXCartStep::class
        ) {
            return [];
        }

        return [
            'step_thirteen',
        ];
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
}
