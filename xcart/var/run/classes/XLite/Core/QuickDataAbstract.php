<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 *  Quick data
 */
abstract class QuickDataAbstract extends \XLite\Base\Singleton implements \Countable
{
    /**
     * Processing chunk length
     */
    const CHUNK_LENGTH = 100;

    /**
     * Memberships
     *
     * @var array
     */
    protected $memberships;

    /**
     * Zones
     *
     * @var array
     */
    protected $zones;

    /**
     * Update quick data
     *
     * @return void
     */
    public function update()
    {
        $i = 0;
        do {
            $processed = $this->updateChunk($i, static::CHUNK_LENGTH);
            if (0 < $processed) {
                \XLite\Core\Database::getEM()->clear();
            }
            $i += $processed;

        } while (0 < $processed);
    }

    /**
     * Update chunk
     *
     * @param integer $position Position OPTIONAL
     * @param integer $length   Length OPTIONAL
     *
     * @return integer
     */
    public function updateChunk($position = 0, $length = self::CHUNK_LENGTH)
    {
        $processed = 0;
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Product')->findFrame($position, $length) as $product) {
            $this->updateProductDataInternal($product);
            $processed++;
        }

        if (0 < $processed) {
            \XLite\Core\Database::getEM()->flush();
        }

        return $processed;
    }

    /**
     * Update chunk
     *
     * @param integer $length Length OPTIONAL
     *
     * @return integer
     */
    public function updateUnprocessedChunk($length = self::CHUNK_LENGTH)
    {
        $processed = 0;
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Product')->findUnprocessedChunk($length) as $product) {
            $this->updateProductDataInternal($product);
            $processed++;
        }
        \XLite\Core\Database::getEM()->flush();
        
        return $processed;
    }

    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->count();
    }

    /**
     * Count unprocessed
     *
     * @return integer
     */
    public function countUnprocessed()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->countUnprocessed();
    }

    /**
     * Update membership quick data
     *
     * @param \XLite\Model\Membership $membership Membership
     *
     * @return void
     */
    public function updateMembershipData(\XLite\Model\Membership $membership)
    {
        $i = 0;
        do {
            $processed = 0;
            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->findFrame($i, static::CHUNK_LENGTH);
            foreach ($products as $product) {
                $this->updateData($product, $membership);
                $processed++;
            }

            if (0 < $processed) {
                \XLite\Core\Database::getEM()->flush();
                \XLite\Core\Database::getEM()->clear();

                $membership = \XLite\Core\Database::getEM()->merge($membership);
            }
            $i += $processed;

        } while (0 < $processed);
    }

    /**
     * Update product quick data
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function updateProductData(\XLite\Model\Product $product)
    {
        $this->updateProductDataInternal($product);
        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Update product quick data
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function updateProductDataInternal(\XLite\Model\Product $product)
    {
        $this->processUpdateProductData($product);
        $product->updateSales();
        $product->setNeedProcess(false);
    }

    /**
     * @param \XLite\Model\Product $product
     */
    protected function processUpdateProductData(\XLite\Model\Product $product)
    {
        foreach ($this->getMemberships() as $membership) {
            if (!isset($membership) || \XLite\Core\Database::getEM()->contains($membership)) {
                $this->updateData($product, $membership);
            }
        }
    }

    /**
     * Get memberships
     *
     * @param \XLite\Model\Product $product    Product
     * @param mixed                $membership Membership
     *
     * @return array
     */
    public function updateData(\XLite\Model\Product $product, $membership)
    {
        $data = [];

        foreach ($this->getZones() as $zone) {
            if (is_null($zone) || \XLite\Core\Database::getEM()->contains($zone)) {
                $data[] = $this->updateDataWithZone($product, $membership, $zone);
            }
        }

        return $data;
    }

    /**
     * Get memberships
     *
     * @param \XLite\Model\Product $product    Product
     * @param mixed                $membership Membership
     * @param mixed                $zone       Zone
     *
     * @return \XLite\Model\QuickData
     */
    protected function updateDataWithZone(\XLite\Model\Product $product, $membership, $zone)
    {
        $data = $this->getProductQuickData($product, $membership, $zone);

        if (!$data) {
            $data = new \XLite\Model\QuickData;
            $data->setProduct($product);
            $data->setMembership($membership);
            $data->setZone($zone);
            $product->addQuickData($data);
        }

        $data->setPrice(\XLite::getInstance()->getCurrency()->roundValue($this->getQuickDataPrice($product, $membership, $zone)));

        return $data;
    }

    /**
     * @param \XLite\Model\Product $product
     * @param $membership
     * @param $zone
     * @return float
     */
    protected function getQuickDataPrice(\XLite\Model\Product $product, $membership, $zone)
    {
        return $product->getQuickDataPrice();
    }

    /**
     * @param \XLite\Model\Product $product
     * @param                      $membership
     * @param                      $zone
     *
     * @return \XLite\Model\QuickData|null
     */
    protected function getProductQuickData(\XLite\Model\Product $product, $membership, $zone)
    {
        $quickData = $product->getQuickData() ?: array();

        $data = null;

        foreach ($quickData as $qd) {
            $isMembershipEqual = ($qd->getMembership()
                    && $membership
                    && $qd->getMembership()->getMembershipId() == $membership->getMembershipId()
                )
                || (!$qd->getMembership() && !$membership);
            $isZoneEqual = ($qd->getZone()
                    && $zone
                    && $qd->getZone()->getZoneId() == $zone->getZoneId()
                )
                || (!$qd->getZone() && !$zone);

            if ($isMembershipEqual && $isZoneEqual) {
                $data = $qd;
                break;
            }
        }

        return $data;
    }

    /**
     * Detach products
     *
     * @param array $products Products
     *
     * @return void
     */
    protected function detachProducts(array $products)
    {
        foreach ($products as $product) {
            \XLite\Core\Database::getEM()->detach($product);
        }
    }

    /**
     * Get memberships
     *
     * @return array
     */
    protected function getMemberships()
    {
        if (!isset($this->memberships)) {
            $this->memberships = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll();
            $this->memberships[] = null;
        }

        return $this->memberships;
    }

    /**
     * @return array
     */
    public function getZones()
    {
        if (!isset($this->zones)) {
            $this->zones = $this->defineZones();
        }

        return $this->zones;
    }

    /**
     * @return array
     */
    protected function defineZones()
    {
        $zones = [];
        $zones[-1] = null;

        return $zones;
    }

    /**
     * @param \XLite\Model\Profile $profile
     * @return mixed|null
     */
    public function getQuickDataZoneForProfile(\XLite\Model\Profile $profile)
    {
        $address = null;
        $qdZone = null;

        $qdZones = $this->getZones();
        if (count($qdZones) > 1 || !array_key_exists(-1, $qdZones)) {
            $addressObj = $profile->getShippingAddress();

            if (!$addressObj) {
                $addressObj = $profile->getBillingAddress();
            }

            if ($addressObj) {
                $address = $addressObj->toArray();
            }

            if (!$address) {
                $address = \XLite\Model\Shipping::getDefaultAddress();
            }

            $zones = $address ? \XLite\Core\Database::getRepo('XLite\Model\Zone')->findApplicableZones($address) : [];
            $qdZoneIds = array_keys($qdZones);

            foreach ($zones as $zone) {
                if (in_array($zone->getZoneId(), $qdZoneIds)) {
                    $qdZone = $zone;
                    break;
                }
            }
        }

        return $qdZone;
    }
}
