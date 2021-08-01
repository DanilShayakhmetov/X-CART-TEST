<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\AXCartStep;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "12000")
 * @RebuildStep(script = "install", weight = "1000")
 */
class XCartStep extends AXCartStep
{
    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getTransitions(ScriptState $scriptState): array
    {
        return [
            'step_first',
            'step_second',
            'step_third',
            'step_fourth',
            'step_fifth',
            'step_six',
            'step_seven',
            //'step_eight',
            'step_nine',
            'step_ten',
            'step_eleven',
            'step_twelve',
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
