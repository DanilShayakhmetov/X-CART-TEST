<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Message;

use Bernard\Message\AbstractMessage as BaseMessage;

abstract class AbstractMessage extends BaseMessage
{
    /**
     * @var float
     */
    protected $sentAt = 0;

    public function __construct()
    {
        $this->sentAt = microtime(true);
    }

    /**
     * @return float
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }
}
