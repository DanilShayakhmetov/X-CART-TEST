<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ColumnType;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Decimal
 */
class Decimal extends \Doctrine\DBAL\Types\DecimalType
{
    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string) (float) $value;
    }

    /**
     * Convert DB value to PHP value
     *
     * @param string           $value    DB value
     * @param AbstractPlatform $platform Platform
     *
     * @return float
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        return $value !== null && !is_double($value) ? doubleval($value) : $value;
    }
}
