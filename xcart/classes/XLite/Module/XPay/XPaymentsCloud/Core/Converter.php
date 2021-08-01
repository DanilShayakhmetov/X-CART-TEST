<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core;

/**
 * Miscellaneous conversion routines
 */
abstract class Converter
{
    /**
     * Return current time.
     *
     * @return int
     */
    public static function now()
    {
        return defined('XPS_START_TIME')
            ? XPS_START_TIME
            : LC_START_TIME;
    }

    /**
     * Validate that param is valid unix timestamp. If not then return current time
     *
     * @param mixed $timestamp Value to check
     *
     * @return mixed
     */
    public static function validateTimestamp($timestamp)
    {
        if (
            !is_numeric($timestamp)
            || intval($timestamp) !== $timestamp
        ) {
            $timestamp = static::now();
        }

        return $timestamp;
    }

    /**
     * Wrapper of system function date()
     *
     * @param string $format Output format
     * @param int $timestamp Unix timestamp
     *
     * @return string
     */
    public static function getDate($format, $timestamp = null)
    {
        $timestamp = static::validateTimestamp($timestamp);

        return date($format, $timestamp);
    }

    /**
     * Return timestamp of midninght for specified day, month and year
     *
     * @param int $month Month
     * @param int $day Day
     * @param int $year Year
     *
     * @return int
     */
    protected static function getMkday($month, $day, $year)
    {
        return mktime(0, 0, 0, $month, $day, $year);
    }

    /**
     * Wrapper of system function strtotime()
     *
     * @param string $time A date/time string
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function getStrtotime($time, $timestamp = null)
    {
        $timestamp = static::validateTimestamp($timestamp);

        return strtotime($time, $timestamp);
    }

    /**
     * Get timestamp of day start
     *
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function convertTimeToDayStart($timestamp = null)
    {
        $timestamp = static::validateTimestamp($timestamp);

        return \XLite\Core\Converter::getDayStart($timestamp);
    }

    /**
     * Get count days of month
     *
     * @param int $timestamp Unix timestamp
     *
     * @return string
     */
    public static function getCountDaysInMonth($timestamp = null)
    {
        return static::getDate('t', $timestamp);
    }

    /**
     * Count of days of year
     *
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function getCountDaysInYear($timestamp = null)
    {
        return 365 + (int)static::getDate('L', $timestamp);
    }

    /**
     * Add several days to the date
     *
     * @param int $count Count of days
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function addDay(int $count, int $timestamp)
    {
        return $timestamp + 86400 * $count;
    }

    /**
     * Add several months to the date
     *
     * @param int $count Count of months
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function addMonth(int $count, int $timestamp): int
    {
        $origMonth = static::getDate('m', $timestamp);
        $destMonth = ((int)$origMonth + $count) % 12;

        $dayNumber = static::getDate('d', $timestamp);

        if ($dayNumber > static::getCountDaysInMonth($destMonth)) {
            // We're getting smth like June, 31 which is considered by PHP as July, 01.
            // So move back to June, 30
            $result = static::getStrtotime('last day of +' . --$count . ' month', $timestamp);
        } else {
            $result = static::getStrtotime('+' . $count . ' month', $timestamp);
        }

        return $result;
    }

    /**
     * Add several years to the date
     *
     * @param int $count Count of years
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function addYear($count, $timestamp)
    {
        // Simply add 12 months for each year.
        return static::addMonth($count * 12, $timestamp);
    }

    /**
     * Set day of month (can return another month)
     *
     * @param int $day Day number
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function setDayOfMonth($day, $timestamp = null)
    {
        $month = static::getDate('n', $timestamp);
        $year = static::getDate('Y', $timestamp);

        return static::getMkday($month, $day, $year);
    }

    /**
     * Set day of year (can return another year)
     *
     * @param int $day Day number
     * @param int $timestamp Unix timestamp
     *
     * @return int
     */
    public static function setDayOfYear($day, $timestamp = null)
    {
        $year = static::getDate('Y', $timestamp);

        $timestamp = static::getMkday(1, 1, $year);

        return static::setDayOfMonth($day, $timestamp);
    }
}
