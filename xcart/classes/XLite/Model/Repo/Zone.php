<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use Includes\Utils\ArrayManager;

/**
 * Zone repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Zone", summary="Add new shipping zone")
 * @Api\Operation\Read(modelClass="XLite\Model\Zone", summary="Retrieve shipping zone by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Zone", summary="Retrieve all shipping zones")
 * @Api\Operation\Update(modelClass="XLite\Model\Zone", summary="Update shipping zone by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Zone", summary="Delete shipping zone by id")
 */
class Zone extends \XLite\Model\Repo\ARepo
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Common search parameters
     */

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'is_default';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['zone_name'],
        ['is_default'],
    ];

    private $addressCache = [];

    // {{{ defineCacheCells

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = [
            static::RELATION_CACHE_CELL => ['\XLite\Model\Zone'],
        ];

        $list['with_special'] = [
            static::RELATION_CACHE_CELL => ['\XLite\Model\Zone'],
        ];

        $list['default'] = [
            static::RELATION_CACHE_CELL => ['\XLite\Model\Zone'],
        ];

        $list['zone'] = [
            static::ATTRS_CACHE_CELL    => ['zone_id'],
            static::RELATION_CACHE_CELL => ['\XLite\Model\Zone'],
        ];

        return $list;
    }

    // }}}

    // {{{ findAllZones

    /**
     * findAllZones
     *
     * @param bool $includeSpecial
     *
     * @return array
     */
    public function findAllZones($includeSpecial = false)
    {
        $type = $includeSpecial ? 'with_special' : 'all';
        $data = $this->getFromCache($type);

        if (!isset($data)) {
            $data = $this->defineFindAllZones($includeSpecial)->getResult();
            $this->saveToCache($data, $type);
        }

        return $data;
    }

    /**
     * defineGetZones
     *
     * @param bool $includeSpecial
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindAllZones($includeSpecial = false)
    {
        return $this->createQueryBuilder()
            ->addSelect('ze')
            ->leftJoin('z.zone_elements', 'ze')
            ->addOrderBy('z.is_default', 'DESC')
            ->addOrderBy('z.zone_name');
    }

    // }}}

    // {{{ findZone

    /**
     * findZone
     *
     * @param integer $zoneId Zone Id
     *
     * @return \XLite\Model\Zone
     */
    public function findZone($zoneId)
    {
        $data = $this->getFromCache('zone', ['zone_id' => $zoneId]);

        if (!isset($data)) {
            $data = $this->defineFindZone($zoneId)->getSingleResult();

            if ($data) {
                $this->saveToCache($data, 'zone', ['zone_id' => $zoneId]);
            }
        }

        return $data;
    }

    /**
     * defineGetZone
     *
     * @param mixed $zoneId Zone id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindZone($zoneId)
    {
        return $this->createQueryBuilder()
            ->addSelect('ze')
            ->leftJoin('z.zone_elements', 'ze')
            ->andWhere('z.zone_id = :zoneId')
            ->setParameter('zoneId', $zoneId);
    }

    // }}}

    // {{{ findApplicableZones

    /**
     * Get the zones list applicable to the specified address
     *
     * @param array $address Address data
     *
     * @return array
     */
    public function findApplicableZones($address)
    {
        $hash = ArrayManager::md5($address);

        if (isset($this->addressCache[$hash])) {
            return $this->addressCache[$hash];
        }

        if (is_numeric($address['state']) &&
            \XLite\Core\Database::getRepo('XLite\Model\State')->getCountByCountryAndStateId($address['country'], $address['state']) !== '0') {
            $address['state'] = \XLite\Core\Database::getRepo('XLite\Model\State')->getCodeById($address['state']);
        }

        // Get all zones list
        $allZones = $this->findAllZones(true);
        $applicableZones = [];

        // Get the list of zones that are applicable for address
        /** @var \XLite\Model\Zone $zone */
        foreach ($allZones as $zone) {
            $zoneWeight = $zone->getZoneWeight($address);

            if (0 < $zoneWeight) {
                $applicableZones[] = [
                    'weight' => $zoneWeight,
                    'zone'   => $zone,
                ];
            }
        }

        // Sort zones list by weight in reverse order
        usort($applicableZones, function ($a, $b) {
            return $a['weight'] == $b['weight']
                ? 0
                : (($a['weight'] > $b['weight']) ? -1 : 1);
        });

        $result = [];
        foreach ($applicableZones as $zone) {
            $result[] = $zone['zone'];
        }

        $this->addressCache[$hash] = $result;

        return $result;
    }

    /**
     * Return default zone
     *
     * @return \XLite\Model\Zone
     */
    protected function getDefaultZone()
    {
        $result = $this->getFromCache('default');

        if (!isset($result)) {
            $result = $this->findOneBy(['is_default' => 1]);
            $this->saveToCache($result, 'default');
        }

        return $result;
    }

    // }}}

    // {{{ Zones list for offline shipping

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    public function findMethodZones($method)
    {
        return $this->findAllZones();
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return array
     */
    public function getOfflineShippingZones($method)
    {
        $allMethodZones = $this->findMethodZones($method);
        $usedZones = $this->getOfflineShippingUsedZones($method);

        $usedList = [];
        $unUsedList = [];

        if ($usedZones) {
            foreach ($allMethodZones as $zone) {
                if (isset($usedZones[$zone->getZoneId()])) {
                    $usedList[$zone->getZoneId()] = sprintf('%s (%d)', $zone->getZoneName(), $usedZones[$zone->getZoneId()]);

                } else {
                    $unUsedList[$zone->getZoneId()] = sprintf('%s (%d)', $zone->getZoneName(), 0);
                }
            }

            if ($usedList) {
                asort($usedList);
                asort($unUsedList);
            }
        } else {
            foreach ($allMethodZones as $zone) {
                $unUsedList[$zone->getZoneId()] = $zone->getZoneName();
            }
        }

        return [$usedList, $unUsedList];
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return array
     */
    protected function getOfflineShippingUsedZones($method)
    {
        $list = [];

        if ($method->getShippingMarkups()) {
            foreach ($method->getShippingMarkups() as $markup) {
                if ($markup->getZone()) {
                    if (!isset($list[$markup->getZone()->getZoneId()])) {
                        $list[$markup->getZone()->getZoneId()] = 1;

                    } else {
                        $list[$markup->getZone()->getZoneId()]++;
                    }
                }
            }
        }

        return $list;
    }

    // }}}
}
