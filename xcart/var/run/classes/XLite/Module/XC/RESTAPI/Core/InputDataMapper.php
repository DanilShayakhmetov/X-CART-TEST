<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core;


use XLite\Module\XC\RESTAPI\Core\Exception\IncorrectInputData;
use XLite\Module\XC\RESTAPI\Core\Exception\IncorrectInputType;

class InputDataMapper
{
    /**
     * @param      $rawData
     * @param      $type
     * @param bool $isMultiple
     *
     * @return mixed
     * @throws \XLite\Module\XC\RESTAPI\Core\Exception\IncorrectInputType
     */
    public function getMapped($rawData, $type, $isMultiple = false)
    {
        $callback = $this->getMapperCallback($type);

        return $callback($rawData, $isMultiple);
    }

    /**
     * @param $type
     *
     * @return callable
     */
    protected function getMapperCallback($type)
    {
        return [$this, $this->getMappedType($type)];
    }

    /**
     * @param $type
     *
     * @return string
     */
    protected function getMappedType($type)
    {
        $mappers = $this->getMappers();
        $mappedType = $this->getDefaultMappedType();

        foreach ($mappers as $k => $v) {
            if (strpos($type, $k) > -1) {
                $mappedType = $v;
            }
        }

        return $mappedType;
    }

    /**
     * @return string
     */
    protected function getDefaultMappedType()
    {
        return 'mapFormEncode';
    }

    /**
     * @return array
     */
    public function getMappers()
    {
        return [
            'application/x-www-form-urlencoded' => 'mapFormEncode',
            'multipart/form-data'               => 'mapFormEncode',
            'application/json'                  => 'mapJson',
        ];
    }

    /**
     * Mapping for x-www-form-urlencoded
     *
     * @param $rawData
     *
     * @param $isMultiple
     *
     * @return array
     */
    protected function mapFormEncode($rawData, $isMultiple)
    {
        $parsed = [];

        parse_str($rawData, $parsed);

        return $parsed;
    }

    /**
     * Mapping for json
     *
     * @param $rawData
     *
     * @param $isMultiple
     *
     * @return array
     * @throws IncorrectInputData
     */
    protected function mapJson($rawData, $isMultiple)
    {
        $result = json_decode($rawData, true);

        if (!$result && !empty($rawData)) {
            throw new IncorrectInputData();
        }

        return $result;
    }
}