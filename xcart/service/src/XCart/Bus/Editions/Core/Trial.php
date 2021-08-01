<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Editions\Core;

use XCart\SilexAnnotations\Annotations\Service;
use Silex\Application;

/**
 * Class Trial
 *
 * @Service\Service()
 */
class Trial
{
    /**
     * @var boolean
     */
    protected $isEnabled;

    /**
     * @var int
     */
    protected $endDateTimestamp;

    /**
     * @var string
     */
    protected $endDateFormat;

    /**
     * @param Application         $app
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app
    ) {
        return new self(
            $app['config']['trial.is_enabled'],
            $app['config']['trial.end_date'],
            'd-m-Y' // End date format
        );
    }

    /**
     * @param string $isEnabled
     * @param string $endDateString
     * @param string $endDateFormat
     */
    public function __construct(
        $isEnabled,
        $endDateString,
        $endDateFormat
    ) {
        $this->isEnabled        = (bool) $isEnabled;
        $this->endDateFormat    = $endDateFormat;
        $this->endDateTimestamp = $endDateString
            ? $this->parseDate($endDateString)
            : null;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        $interval = $this->getLeftInterval();

        return !$interval || static::dateIntervalToSeconds($interval) <= 0;
    }

    /**
     * @return bool|\DateInterval
     */
    public function getLeftInterval()
    {
        $endTimestamp = static::getDayStart(time());
        if ($this->endDateTimestamp) {
            $endTimestamp = static::getDayEnd(
                $this->endDateTimestamp
            );
        }

        $now = \DateTime::createFromFormat('U', time());
        $end = \DateTime::createFromFormat('U', $endTimestamp);

        $diff = $now->diff($end);
        return $diff->invert
            ? false
            : $diff;
    }

    /**
     * @param $dateInterval
     *
     * @return int
     */
    protected static function dateIntervalToSeconds($dateInterval)
    {
        if(!$dateInterval) {
            return 0;
        }

        $reference = new \DateTimeImmutable;
        $endTime = $reference->add($dateInterval);

        return $endTime->getTimestamp() - $reference->getTimestamp();
    }

    /**
     * @param string $endDateString
     *
     * @return integer
     */
    protected function parseDate($endDateString)
    {
        return \DateTime::createFromFormat($this->endDateFormat, $endDateString)->getTimestamp();
    }

    /**
     * @param $timestamp
     *
     * @return int
     */
    protected static function getDayStart($timestamp)
    {
        return \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            (new \DateTime())->setTimestamp($timestamp)->format('Y-m-d 00:00:00')
        )->getTimestamp();
    }

    /**
     * @param $timestamp
     *
     * @return int
     */
    protected static function getDayEnd($timestamp)
    {
        return \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            (new \DateTime())->setTimestamp($timestamp)->format('Y-m-d 23:59:59')
        )->getTimestamp();
    }
}
