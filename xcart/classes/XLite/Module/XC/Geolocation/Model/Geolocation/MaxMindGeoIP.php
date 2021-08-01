<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Model\Geolocation;

use XLite\Module\XC\Geolocation\lib\MaxMind;
use XLite\Module\XC\Geolocation\Logic;

/**
 * MaxMind geolocation provider
 */
class MaxMindGeoIP extends AProvider
{
    public function __construct()
    {
        $this->includeLibrary();
    }

    /**
     * Returns geolocation data in raw format (defined by provider)
     *
     * @param Logic\IGeoInput $data
     *
     * @return \GeoIp2\Model\AbstractModel[]
     */
    public function getRawLocation(Logic\IGeoInput $data)
    {
        if (!($data instanceof Logic\GeoInput\IpAddress)) {
            return null;
        }

        $records = [];

        try {
            $reader = $this->getReader();
            try {
                $records[] = $reader->city($data->getData());
            } catch (\Exception $e) {
                $records[] = $reader->country($data->getData());
            }
        } catch (\Exception $e) {
            $record = null;
        }

        return $records;
    }

    /**
     * Returns human readable provider name.
     *
     * @return string
     */
    public function getProviderName()
    {
        return 'MaxMind GeoIP2';
    }

    /**
     * Returns list of accepted geo input types.
     *
     * @return array
     */
    public function acceptedInput()
    {
        return ['IpAddress'];
    }

    /**
     * Transforms raw geolocation data to XCart format (array of address fields)
     *
     * @param \GeoIp2\Model\AbstractModel[] $data
     *
     * @return array
     */
    protected function transformData($data)
    {
        $result = [];

        foreach ($data as $record) {
            if ($record instanceof \GeoIp2\Model\Country) {
                $result['country'] = $record->country->isoCode;
            }

            if ($record instanceof \GeoIp2\Model\City) {
                $result['country'] = $record->country->isoCode;
                $result['city'] = $record->city->name;

                if ($record->mostSpecificSubdivision->isoCode) {
                    $result['state'] = $record->mostSpecificSubdivision->isoCode;
                }

                if ($record->mostSpecificSubdivision->name) {
                    $result['custom_state'] = $record->mostSpecificSubdivision->name;
                }

                if ($record->postal->code) {
                    $result['zipcode'] = $record->postal->code;
                }
            }
        }

        return $result;
    }

    /**
     * Returns MaxMind reader object with loaded database.
     *
     * @return \GeoIp2\Database\Reader
     */
    protected function getReader()
    {
        return new \GeoIp2\Database\Reader($this->getGeoDb());
    }

    /**
     * Returns GeoLite2-Country db path.
     *
     * @return string
     */
    protected function getGeoDb()
    {
        $extended_db_path = \XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path;
        if ($extended_db_path && file_exists($extended_db_path)) {
            return $extended_db_path;
        }
        return static::getDefaultDatabasePath();
    }

    /**
     * Returns default GeoLite2-Country db path.
     *
     * @return string
     */
    public static function getDefaultDatabasePath()
    {
        return LC_DIR_MODULES . 'XC' . LC_DS . 'Geolocation' . LC_DS . 'lib' . LC_DS . 'MaxMind' . LC_DS . 'GeoLite2-Country.mmdb';
    }

    /**
     * Loads MaxMind php reader
     */
    protected function includeLibrary()
    {
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'Geolocation' . LC_DS . 'lib' . LC_DS . 'MaxMind' . LC_DS . 'geoip2.phar';
    }

}