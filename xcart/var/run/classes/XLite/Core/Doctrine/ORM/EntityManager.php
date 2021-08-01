<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\ORM;

use Doctrine\DBAL\Connection;


/**
 * EntityManager
 */
class EntityManager extends \Doctrine\ORM\EntityManager
{
    /**
     * Transactional with restarts @see transactional()
     *
     * @param callable $func
     * @param callable|null $rollbackCallback used
     * @param int $triesCount 1 equal to transactional()
     *
     * @return mixed
     * @throws \Exception
     */
    public function transactionalWithRestarts(callable $func, $rollbackCallback = null, $triesCount = 3)
    {
        try {
            $this->getConnection()->beginTransaction();

            $return = $func($this);

            $this->flush();
            $this->getConnection()->commit();

            return $return ?: true;
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            if (is_callable($rollbackCallback)) {
                $rollbackCallback($this);
            }

            if ($triesCount > 1) {
                return $this->transactionalWithRestarts($func, $rollbackCallback, $triesCount - 1);
            }

            $this->close();
            throw $e;
        }
    }

    /**
     * @param $func
     * @return bool|mixed
     * @throws \Exception
     */
    public function transactional($func)
    {
        $this->getConnection()->setTransactionIsolation(Connection::TRANSACTION_READ_COMMITTED);

        return parent::transactional($func);
    }

    /**
     * @param null|object|array $entity
     *
     * @throws \Exception
     */
    public function flush($entity = null)
    {
        try {
            parent::flush();

        } catch (\Exception $e) {
            if (!$this->isOpen()) {
                \XLite\Logger::getInstance()->registerException($e);
            }

            throw $e;
        }
    }
}