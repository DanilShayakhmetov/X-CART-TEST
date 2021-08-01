<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\System;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ResourceChecker
{
    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $maxExecutionTime;

    private $systemMemoryLimit;

    public function __construct()
    {
        $this->maxExecutionTime = ((ini_get('max_execution_time') ?: 30) * 10000);
        $this->systemMemoryLimit = $this->getMemoryLimit();
    }

    public function start(): void
    {
        $this->startTime = $this->getTime();
    }

    /**
     * @return int
     */
    public function timeRemain(): int
    {
        return $this->maxExecutionTime - $this->getTimePassed();
    }

    /**
     * @return int
     */
    public function getTimePassed(): int
    {
        return $this->getTime() - $this->startTime;
    }

    /**
     * @return int
     */
    public function getIntegerLength(): int
    {
        return PHP_INT_SIZE;
    }

    /**
     * @param string $limit
     */
    public function setMemoryLimit($limit): bool
    {
        $limitInBytes = $this->convertToBytes($limit);
        if ($limitInBytes > $this->systemMemoryLimit) {
            ini_set('memory_limit', $limitInBytes);
        }

        return $this->getMemoryLimit() === $limitInBytes;
    }

    /**
     * @return int
     */
    public function getMemoryLimit(): int
    {
        return $this->convertToBytes(ini_get('memory_limit'));
    }

    /**
     * @return int
     */
    private function getTime(): int
    {
        return (int)(microtime(true) * 10000);
    }

    /**
     * @param string $memoryLimit
     *
     * @return int
     * @see \Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector::convertToBytes
     */
    private function convertToBytes($memoryLimit): int
    {
        if ('-1' === $memoryLimit) {
            return -1;
        }

        $memoryLimit = strtolower($memoryLimit);
        $max = strtolower(ltrim($memoryLimit, '+'));
        if (0 === strpos($max, '0x')) {
            $max = intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr($memoryLimit, -1)) {
            case 't': $max *= 1024;
            // no break
            case 'g': $max *= 1024;
            // no break
            case 'm': $max *= 1024;
            // no break
            case 'k': $max *= 1024;
        }

        return $max;
    }

    /**
     * @return boolean
     */
    public static function PharIsInstalled(): bool
    {
        return extension_loaded('Phar');
    }
}
