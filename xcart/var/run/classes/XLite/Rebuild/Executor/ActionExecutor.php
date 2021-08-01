<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild\Executor;

class ActionExecutor implements IRebuildExecutor
{
    /**
     * @param mixed $payloadData
     *
     * @return bool
     */
    public function isApplicable($payloadData)
    {
        return !empty($payloadData['action']);
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
        $action = $this->populateFromPayload($payloadData);
        $action->process();

        return [
            'state' => $action->isFinished()
                ? 'finished'
                : 'in_progress',
        ];
    }

    /**
     * @param $data
     *
     * @return \XLite\Rebuild\Executor\Entry\UpgradeAction
     * @throws \Exception
     */
    protected function populateFromPayload($data)
    {
        $action = $data['action'];
        unset($data['action']);

        if ($action === 'upgrade') {
            return new Entry\UpgradeAction($data);
        }

        throw new \Exception();
    }
}