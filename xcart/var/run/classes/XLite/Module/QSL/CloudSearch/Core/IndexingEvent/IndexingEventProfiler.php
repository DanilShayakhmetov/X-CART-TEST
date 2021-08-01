<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core\IndexingEvent;

use XLite\Logger;


class IndexingEventProfiler extends \XLite\Base\Singleton
{
    protected $sendTime = 0;

    protected $totalTime = 0;

    public function addToTotalTime($t)
    {
        $this->totalTime += $t;
    }

    public function addToSendTime($t)
    {
        $this->sendTime += $t;
    }

    public function log()
    {
        $totalTime = round($this->totalTime * 1000);
        $sendTime  = round($this->sendTime * 1000);

        if ($totalTime > 10) {
            Logger::logCustom('CloudSearchEvents', "$totalTime $sendTime");
        }
    }
}