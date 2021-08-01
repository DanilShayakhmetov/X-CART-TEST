<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job\State\Registry;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use XLite\Core\Job\State\JobStateInterface;
use XLite\Core\Job\State\StateRegistryFactory;

/**
 * Class DatabaseDrivenRegistry
 * TODO Consider use (symfony) serialization here
 */
class DatabaseDrivenRegistry implements StateRegistryInterface
{
    /**
     * @param $id   int     Job id
     *
     * @return \XLite\Model\Job\State
     */
    public function get($id)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Job\State')->find($id);
    }

    /**
     * @param int               $id
     * @param JobStateInterface $state
     */
    public function set($id, JobStateInterface $state)
    {
        $tableName = \XLite\Core\Database::getRepo('XLite\Model\Job\State')->getTableName();

        if (!$this->get($id)) {
            $data = $state->toArrayForNative();
            $data['id'] = $id;

            \XLite\Core\Database::getEM()->getConnection()->insert(
                $tableName,
                $data
            );

        } else {
            if ($this->get($id) !== $state) {
                $this->get($id)->map($state->toArray());
            }
            $data = $this->get($id)->toArrayForNative();

            $query = 'UPDATE ' . $tableName
                . ' SET ' . implode(', ', array_map(function ($v) {
                    return $v . ' = ?';
                }, array_keys($data)))
                . ' WHERE id = ?';
            $data[] = $id;

            \XLite\Core\Database::getEM()->getConnection()->executeUpdate($query, array_values($data));
        }
    }

    /**
     * @param $jobId
     *
     * @return string
     */
    protected function getStateCellName($id)
    {
        return sprintf('job_%s_state', $id);
    }

    /**
     * @param $callback callable
     *
     * @return mixed
     */
    public function process($id, $callback)
    {
        return \XLite\Core\Database::getEM()->transactional(function (EntityManager $em) use ($id, $callback) {
            $jobState = $this->get($id);

            if (!$jobState) {
                StateRegistryFactory::initiate($id);
                $jobState = $this->get($id);
            }

            if (!$jobState) {
                throw new JobStateNotFoundException('No job state found for job #' . $id);
            }

            $em->lock($jobState, LockMode::PESSIMISTIC_WRITE);
            $em->refresh($jobState);

            $result = call_user_func_array($callback, [
                $id,
                $jobState,
            ]);

            if (!$jobState) {
                throw new JobStateNotFoundException('No correct job state was returned for job #' . $id);
            }

            $this->set($id, $jobState);

            return $result;
        });
    }
}
