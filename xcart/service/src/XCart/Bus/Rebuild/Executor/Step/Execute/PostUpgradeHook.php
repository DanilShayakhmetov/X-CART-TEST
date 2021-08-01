<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Module;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\AUpgradeHook;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "9000")
 */
class PostUpgradeHook extends AUpgradeHook
{
    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getTransitions(ScriptState $scriptState): array
    {
        $transitions = [];

        $parentStepState = $scriptState->getCompletedStepState(CheckPostponedHooks::class);
        if ($parentStepState) {
            $executePostponedHooksModules = $parentStepState->data['executeModulesHooks'];

            foreach (parent::getTransitions($scriptState) as $id => $transition) {
                if (Module::isPreviuosMajorVersion($transition['version_before'], $transition['version_after'])
                    && !in_array($transition['id'], $executePostponedHooksModules, true)
                ) {
                    continue;
                }

                $transitions[$id] = $transition;
            }
        } else {
            $transitions = parent::getTransitions($scriptState);
        }

        return $transitions;
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'post_upgrade';
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
