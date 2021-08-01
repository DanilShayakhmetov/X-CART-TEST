<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use XLite\Core\Database;

/**
 * Temporary variables repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\TmpVar", summary="Add new temporary variable")
 * @Api\Operation\Read(modelClass="XLite\Model\TmpVar", summary="Retrieve temporary variable by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\TmpVar", summary="Retrieve all temporary variables")
 * @Api\Operation\Update(modelClass="XLite\Model\TmpVar", summary="Update temporary variable by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\TmpVar", summary="Delete temporary variable by id")
 */
class TmpVar extends \XLite\Model\Repo\ARepo
{
    /**
     * Event task state prefix
     */
    const EVENT_TASK_STATE_PREFIX = 'eventTaskState.';

    /**
     * Set variable
     *
     * @param string  $name  Variable name
     * @param mixed   $value Variable value
     * @param boolean $flush Perform flush on return
     */
    public function setVar($name, $value, $flush = true)
    {
        $this->removeEntityFromUoW($name);
        $connection = Database::getEM()->getConnection();

        $query = 'REPLACE INTO ' . $this->getTableName()
            . ' set `name` = ?, `value` = ?';

        $connection->executeUpdate($query, [$name, serialize($value)]);
    }

    /**
     * Get variable
     *
     * @param string $name Variable name
     *
     * @return mixed
     */
    public function getVar($name)
    {
        $entity = $this->findOneBy(['name' => $name]);

        $value = $entity ? $entity->getValue() : null;

        if (!empty($value)) {
            $tmp = @unserialize($value);
            if (false !== $tmp || $value === serialize(false)) {
                $value = $tmp;
            }
        }

        return $value;
    }

    /**
     * @param $name
     */
    public function removeVar($name)
    {
        $this->removeEntityFromUoW($name);
        $connection = Database::getEM()->getConnection();

        $query = 'DELETE FROM ' . $this->getTableName()
            . ' WHERE `name` = ?';

        $connection->executeUpdate($query, [$name]);
    }

    protected function removeEntityFromUoW($name)
    {
        if ($entity = Database::getRepo('XLite\Model\TmpVar')->tryToFindEntityInIMByCriteria(['name' => $name])) {
            Database::getEM()->getUnitOfWork()->removeFromIdentityMap($entity);
        }
    }

    // {{{ Event tasks-based temporary variable operations

    /**
     * Initialize event task state
     *
     * @param string $name    Event task name
     * @param array  $options Event options OPTIONAL
     *
     * @return array
     */
    public function initializeEventState($name, array $options = [])
    {
        $this->setEventState(
            $name,
            ['position' => 0, 'length' => 0, 'state' => \XLite\Core\EventTask::STATE_STANDBY] + $options
        );
    }

    /**
     * Get event task state
     *
     * @param string $name Event task name
     *
     * @return array
     */
    public function getEventState($name)
    {
        return $this->getVar(static::EVENT_TASK_STATE_PREFIX . $name);
    }

    /**
     * Set event state
     *
     * @param string  $name  Event task name
     * @param array   $rec   Event task state
     * @param boolean $flush Flush task
     *
     * @return void
     */
    public function setEventState($name, array $rec, $flush = true)
    {
        $this->setVar(static::EVENT_TASK_STATE_PREFIX . $name, $rec, $flush);
    }

    /**
     * Set event state
     *
     * @param string $name Event task name
     *
     * @return void
     */
    public function removeEventState($name)
    {
        $this->removeVar(static::EVENT_TASK_STATE_PREFIX . $name);
    }

    /**
     * Check event state - finished or not
     *
     * @param string $name Event task name
     *
     * @return boolean
     */
    public function isFinishedEventState($name)
    {
        $record = $this->getEventState($name);

        return $record
            && ((int) $record['state'] === \XLite\Core\EventTask::STATE_FINISHED
                || (int) $record['state'] === \XLite\Core\EventTask::STATE_ABORTED);
    }

    /**
     * Check event state - finished or not
     *
     * @param string $name Event task name
     *
     * @return boolean
     */
    public function getEventStatePercent($name)
    {
        $percent = 0;

        $record = $this->getEventState($name);
        if ($record) {
            if ($this->isFinishedEventState($name)) {
                $percent = 100;

            } elseif (0 < $record['length']) {
                $percent = min(100, (int) ($record['position'] / $record['length'] * 100));
            }
        }

        return $percent;
    }

    // }}}
}
