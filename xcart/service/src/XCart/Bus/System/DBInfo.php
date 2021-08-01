<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\System;

use PDO;
use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class DBInfo
{
    /**
     * @var array
     */
    private $dbDetails;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $error;

    /**
     * @param Application $app
     *
     * @return DBInfo
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(Application $app): DBInfo
    {
        return new self($app['xc_config']['database_details'] ?? []);
    }

    /**
     * @param array $dbDetails
     */
    public function __construct(array $dbDetails)
    {
        $this->dbDetails = $dbDetails;
    }

    /**
     * @return string
     */
    public function getDBVersion(): string
    {
        try {
            $pdo = $this->getPDO();

            return $pdo ? $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) : '';

        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }

        return '';
    }

    /**
     * @return PDO|null
     */
    private function getPDO(): ?PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->generatePDO();
        }

        return $this->pdo;
    }

    /**
     * @return PDO|null
     */
    private function generatePDO(): ?PDO
    {
        $options = $this->dbDetails;

        $dsn = $this->getDSN();

        $username = $options['username'] ?? '';
        $password = $options['password'] ?? '';

        $options = [
            PDO::ATTR_AUTOCOMMIT => true,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false,
        ];

        try {
            return new PDO(
                $dsn,
                $username,
                $password,
                $options
            );
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    private function getDSN(): string
    {
        $options = $this->dbDetails;

        $dsnFields = array_unique([
            'host'        => $options['hostspec'] ?? '',
            'port'        => $options['port'] ?? '',
            'unix_socket' => $options['socket'] ?? '',
            'dbname'      => $options['database'] ?? '',
        ]);

        $dsnParts = [];
        foreach ($dsnFields as $name => $value) {
            $dsnParts[] = $name . '=' . $value;
        }

        return 'mysql:' . implode(';', $dsnParts);
    }
}
