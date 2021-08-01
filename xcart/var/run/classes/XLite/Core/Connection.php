<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Doctrine-based connection
 */
class Connection extends \Doctrine\DBAL\Connection
{
    /**
     * Connection constructor.
     *
     * @param array                 $params
     * @param \Doctrine\DBAL\Driver $driver
     * @param null                  $config
     * @param null                  $eventManager
     */
    public function __construct(array $params, \Doctrine\DBAL\Driver $driver, $config = null, $eventManager = null)
    {
        parent::__construct($params, $driver, $config, $eventManager);
        $this->setNestTransactionsWithSavepoints(true);
    }

    /**
     * Prepares an SQL statement
     *
     * @param string $statement The SQL statement to prepare
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function prepare($statement, $options = null)
    {
        $this->connect();

        return new \XLite\Core\Statement($statement, $this);
    }

    /**
     * Executes an, optionally parameterized, SQL query.
     *
     * If the query is parameterized, a prepared statement is used.
     * If an SQLLogger is configured, the execution is logged.
     *
     * @param string                                 $query  The SQL query to execute
     * @param array                                  $params The parameters to bind to the query, if any OPTIONAL
     * @param array                                  $types  The parameters types to bind to the query, if any OPTIONAL
     * @param \Doctrine\DBAL\Cache\QueryCacheProfile $qcp    Cache profile OPTIONAL
     *
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \XLite\Core\PDOException
     */
    public function executeQuery(
        $query,
        array $params = array(),
        $types = array(),
        \Doctrine\DBAL\Cache\QueryCacheProfile $qcp = null
    ) {
        try {
            $result = parent::executeQuery($query, $params, $types, $qcp);

        } catch (\PDOException $exception) {
            throw new \XLite\Core\PDOException($exception, $query, $params);
        }

        return $result;
    }

    /**
     * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters
     * and returns the number of affected rows.
     *
     * This method supports PDO binding types as well as DBAL mapping types.
     *
     * @param string $query  The SQL query
     * @param array  $params The query parameters OPTIONAL
     * @param array  $types  The parameter types OPTIONAL
     *
     * @return integer The number of affected rows
     * @throws \XLite\Core\PDOException
     */
    public function executeUpdate($query, array $params = array(), array $types = array())
    {
        try {
            $result = parent::executeUpdate($query, $params, $types);

        } catch (\PDOException $e) {
            throw new \XLite\Core\PDOException($e, $query, $params);
        }

        return $result;
    }

    /**
     * Replace query
     *
     * @param string $tableName Table name
     * @param array  $data      Data
     *
     * @return integer
     */
    public function replace($tableName, array $data)
    {
        $this->connect();

        // column names are specified as array keys
        $cols = array();
        $placeholders = array();

        foreach ($data as $columnName => $value) {
            $cols[] = $columnName;
            $placeholders[] = '?';
        }

        $query = 'REPLACE INTO ' . $tableName
               . ' (' . implode(', ', $cols) . ')'
               . ' VALUES (' . implode(', ', $placeholders) . ')';

        return $this->executeUpdate($query, array_values($data));
    }
}
