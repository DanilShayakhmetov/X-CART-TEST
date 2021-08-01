<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild\Executor;

class HookExecutor implements IRebuildExecutor
{
    /**
     * @param $data
     *
     * @return \XLite\Rebuild\Executor\Entry\Hook
     * @throws \Exception
     */
    protected function populateFromPayload($data)
    {
        $file = $data['file'];
        unset($data['file']);

        $initialized = isset($data['initialized']) ? $data['initialized'] : false;
        unset($data['initialized']);

        return new Entry\Hook($file, $data, $initialized);
    }

    /**
     * @param mixed $payloadData
     *
     * @return bool
     */
    public function isApplicable($payloadData)
    {
        return !empty($payloadData['file']);
    }

    /**
     * @param mixed  $payloadData
     * @param string $rebuildId
     *
     * @return array
     * @throws \Exception
     */
    public function execute($payloadData, $rebuildId)
    {
        $hook = $this->populateFromPayload($payloadData);
        $hook->process();

        return [
            'hookState'   => $hook->getState(),
            'initialized' => $hook->isInitialized(),
            'state'       => $hook->isFinished()
                ? 'finished'
                : 'in_progress',
        ];
    }
}