<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ModulesManager;

class RestorePointsRepository
{
    /**
     * Restore point date internal format
     */
    const RESTORE_DATE_FORMAT = 'Y_m_d_H_i_s';

    /**
     * Get modules migration log file name
     *
     * @return string
     */
    protected static function getRestorePointsPath()
    {
        return LC_DIR_SERVICE . '.restore.points.php';
    }

    /**
     * Returns restore point from migration log if it exists
     *
     * @param string $date restore point date in RESTORE_DATE_FORMAT format
     *
     * @return array
     */
    public static function getRestorePoint($date)
    {
        $restorePoint = null;
        $migrations = static::getRestorePoints();
        if (!empty($migrations) && static::isRestorePointValid($migrations[$date])) {
            $restorePoint = $migrations[$date];
        }
        return $restorePoint;
    }

    /**
     * Returns empty restore point structure to be filled later
     *
     * @return array
     */
    public static function getEmptyRestorePoint()
    {
        return [
            'date'      => date(static::RESTORE_DATE_FORMAT),
            'current'   => [],
            'enabled'   => [],
            'disabled'  => [],
            'deleted'   => [],
            'installed' => [],
        ];
    }

    /**
     * Returns latest restore point if exist
     *
     * @return array|null
     */
    public static function getLatestRestorePoint()
    {
        $restorePoint = end(static::getRestorePoints());

        return $restorePoint && static::isRestorePointValid($restorePoint)
            ? $restorePoint
            : null;
    }

    /**
     * Checks if snapshot is valid
     *
     * @param array $point restore point data
     *
     * @return boolean
     */
    public static function isRestorePointValid($point)
    {
        return $point && is_array($point) && isset($point['date']) && isset($point['current']);
    }

    /**
     * Returns list of restore point
     *
     * @return array
     */
    public static function getRestorePoints()
    {
        return (array)\Includes\Utils\Operator::loadServiceYAML(static::getRestorePointsPath()) ?: [];
    }

    /**
     * Store restore point
     *
     * @param array|null $restorePoint modules migration data
     */
    public static function saveRestorePoint($restorePoint)
    {
        $restorePoints = static::getRestorePoints();

        $restorePoints[$restorePoint['date']] = $restorePoint;

        \Includes\Utils\Operator::saveServiceYAML(static::getRestorePointsPath(), $restorePoints);
    }
}