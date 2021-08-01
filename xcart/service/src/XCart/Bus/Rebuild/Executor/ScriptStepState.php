<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor;

use XCart\Bus\Domain\PropertyBag;

/**
 * @property string  $id                      Step ID (usually class name)
 * @property integer $index                   Step index
 * @property string  $status                  Step state
 * @property int     $timeStart               Step start time
 * @property int     $timeEnd                 Step end time
 * @property string  $error                   Step errors
 */
class ScriptStepState extends PropertyBag
{
    public const STATUS_INITIALIZED = 'initialized';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_FINISHED    = 'finished';
    public const STATUS_ERROR       = 'error';

    /**
     * ScriptStepState constructor.
     *
     * @param string $id    Step ID
     * @param int    $index Step index
     */
    public function __construct(string $id, int $index)
    {
        parent::__construct([
            'id'     => $id,
            'status' => static::STATUS_INITIALIZED,
            'index'  => $index,
        ]);
    }

    /**
     * @return $this
     */
    public function start()
    {
        $this->status    = static::STATUS_IN_PROGRESS;
        $this->timeStart = time();

        return $this;
    }

    /**
     * @return $this
     */
    public function done()
    {
        $this->status  = static::STATUS_FINISHED;
        $this->timeEnd = time();

        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function error(string $message)
    {
        $this->status  = static::STATUS_ERROR;
        $this->error   = $message;
        $this->timeEnd = time();

        return $this;
    }

    /**
     * @return bool
     */
    public function isRunning():bool
    {
        return $this->status === static::STATUS_IN_PROGRESS;
    }
}