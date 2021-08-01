<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Job;

/**
 * Job state
 *
 * @Entity
 * @Table (name="job_states")
 *
 * @HasLifecycleCallbacks
 */
class State extends \XLite\Model\AEntity implements \XLite\Core\Job\State\JobStateInterface
{
    /**
     * ID
     *
     * @var string
     *
     * @Id
     * @Column (type="string")
     */
    protected $id;

    /**
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $progress = 0;

    /**
     * @var integer
     *
     * @Column (type="decimal")
     */
    protected $startedAt = 0;

    /**
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $cancelled = false;

    /**
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $finished = false;

    /**
     * @var array
     *
     * @Column (type="array")
     */
    protected $data = [];

    /**
     * Return Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return Progress
     *
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set Progress
     *
     * @param int $progress
     *
     * @return $this
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
        return $this;
    }

    /**
     * Return StartedAt
     *
     * @return int
     */
    public function isStarted()
    {
        return $this->getStartedAt() > 0;
    }

    /**
     * Return StartedAt
     *
     * @return int
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set StartedAt
     *
     * @param int $startedAt
     *
     * @return $this
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * Return Cancelled
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * Set Cancelled
     *
     * @param bool $cancelled
     *
     * @return $this
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
        return $this;
    }

    /**
     * Return Finished
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * Set Finished
     *
     * @param bool $finished
     *
     * @return $this
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
        return $this;
    }

    /**
     * Return Data
     *
     * @param string $name
     *
     * @return string
     */
    public function getData($name = null)
    {
        if (null === $name) {
            return $this->data;
        }

        return isset($this->data[$name])
            ? $this->data[$name]
            : null;
    }

    /**
     * Set Data
     *
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setData($name = null, $value)
    {
        if (null === $name) {
            $this->data = $value;
        } else {
            $this->data[$name] = $value;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            'progress'  => $this->getProgress(),
            'startedAt' => $this->getStartedAt(),
            'cancelled' => $this->isCancelled(),
            'finished'  => $this->isFinished(),
            'data'      => $this->getData(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function toArrayForNative()
    {
        return [
            'progress'  => $this->getProgress(),
            'startedAt' => $this->getStartedAt(),
            'cancelled' => $this->isCancelled() ? 1 : 0,
            'finished'  => $this->isFinished() ? 1 : 0,
            'data'      => serialize($this->getData()),
        ];
    }
}
