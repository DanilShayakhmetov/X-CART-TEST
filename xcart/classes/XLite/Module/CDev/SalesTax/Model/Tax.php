<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Model;

use XLite\Module\CDev\SalesTax\Model\Tax\Rate;

/**
 * Tax
 *
 * @Entity
 * @Table  (name="sales_taxes")
 */
class Tax extends \XLite\Model\Base\I18n
{
    /**
     * Tax unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Enabled
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Tax rates (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\SalesTax\Model\Tax\Rate", mappedBy="tax", cascade={"all"})
     * @OrderBy ({"position" = "ASC"})
     */
    protected $rates;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->rates = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get filtered general tax rates by zones and membership
     *
     * @param array                   $zones      Zone id list
     * @param \XLite\Model\Membership $membership Membership OPTIONAL
     * @param \XLite\Model\TaxClass   $taxClass   Tax class OPTIONAL
     *
     * @return array
     */
    public function getFilteredRates(
        array $zones,
        \XLite\Model\Membership $membership = null,
        \XLite\Model\TaxClass $taxClass = null
    ) {
        $rates = $this->getApplicableRates($zones, $membership, $taxClass);

        foreach ($rates as $k => $rate) {
            if (\XLite\Module\CDev\SalesTax\Model\Tax\Rate::TAXBASE_SHIPPING == $rate->getTaxableBase()) {
                unset($rates[$k]);
            }
        }

        return $rates;
    }

    /**
     * Get filtered tax rates on shipping cost by zones and membership
     *
     * @param array                   $zones      Zone id list
     * @param \XLite\Model\Membership $membership Membership OPTIONAL
     * @param \XLite\Model\TaxClass   $taxClass   Tax class OPTIONAL
     *
     * @return array
     */
    public function getFilteredShippingRates(
        array $zones,
        \XLite\Model\Membership $membership = null,
        \XLite\Model\TaxClass $taxClass = null
    ) {
        $rates = $this->getApplicableRates($zones, $membership, $taxClass);

        foreach ($rates as $k => $rate) {
            if (\XLite\Module\CDev\SalesTax\Model\Tax\Rate::TAXBASE_SHIPPING != $rate->getTaxableBase()) {
                unset($rates[$k]);
            }
        }

        return $rates;
    }

    /**
     * Get filtered rate by zones and membership
     *
     * @param array                   $zones      Zone id list
     * @param \XLite\Model\Membership $membership Membership OPTIONAL
     * @param \XLite\Model\TaxClass   $taxClass   Tax class OPTIONAL
     *
     * @return \XLite\Module\CDev\SalesTax\Model\Tax\Rate
     */
    public function getFilteredRate(
        array $zones,
        \XLite\Model\Membership $membership = null,
        \XLite\Model\TaxClass $taxClass = null
    ) {
        $rates = $this->getFilteredRates($zones, $membership, $taxClass);

        return array_shift($rates);
    }

    /**
     * Get applicable tax rates by zones and membership
     *
     * @param array                   $zones      Zone id list
     * @param \XLite\Model\Membership $membership Membership OPTIONAL
     * @param \XLite\Model\TaxClass   $taxClass   Tax class OPTIONAL
     *
     * @return array
     */
    protected function getApplicableRates(
        array $zones,
        \XLite\Model\Membership $membership = null,
        \XLite\Model\TaxClass $taxClass = null
    ) {
        $rates = array();

        $ratesList = array();

        foreach ($this->getRates() as $rate) {
            foreach ($zones as $i => $zone) {
                if ($rate->isApplied(array($zone), $membership, $taxClass)) {
                    $ratesList[] = array(
                        'rate'      => $rate,
                        'zoneWeight' => $i,
                        'rateMembershipId'  => $rate->getMembership() ? $rate->getMembership()->getMembershipId() : 0,
                    );
                    break;
                }
            }
        }

        usort($ratesList, array($this, 'sortRates'));

        foreach ($ratesList as $rate) {
            $rates[] = $rate['rate'];
        }

        return $rates;
    }

    /**
     * Sort rates
     *
     * @param array $a Rate A
     * @param array $b Rate B
     *
     * @return boolean
     */
    protected function sortRates($a, $b)
    {
        /* @var Rate $aRate */
        $aRate = $a['rate'];
        /* @var Rate $bRate */
        $bRate = $b['rate'];

        $classesSortResult = $this->compareByTaxClasses(
            $aRate->getTaxClass(),
            $bRate->getTaxClass(),
            $aRate->getNoTaxClass(),
            $bRate->getNoTaxClass()
        );

        if (0 !== $classesSortResult) {
            return $classesSortResult;
        }

        if ($a['zoneWeight'] > $b['zoneWeight']) {
            $result = 1;

        } elseif ($a['zoneWeight'] < $b['zoneWeight']) {
            $result = -1;

        } else {
            $result = $a['rateMembershipId'] > $b['rateMembershipId'] ? -1 : (int) ($a['rateMembershipId'] < $b['rateMembershipId']);
        }

        return $result;
    }

    /**
     * Comparator for rates by tax classes
     *
     * @param $aClass
     * @param $bClass
     * @param $aDefaultClass
     * @param $bDefaultClass
     *
     * @return int
     */
    protected function compareByTaxClasses($aClass, $bClass, $aDefaultClass, $bDefaultClass)
    {
        if (
            $aClass && $bClass
            || $aDefaultClass && $bDefaultClass
            || !$aClass && !$bClass && !$aDefaultClass && !$bDefaultClass
        ) {
            return 0;
        }

        if ($aClass || $bClass) {
            return $aClass ? -1 : 1;
        }

        return $aDefaultClass ? -1 : 1;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Tax
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add rates
     *
     * @param \XLite\Module\CDev\SalesTax\Model\Tax\Rate $rates
     * @return Tax
     */
    public function addRates(\XLite\Module\CDev\SalesTax\Model\Tax\Rate $rates)
    {
        $this->rates[] = $rates;
        return $this;
    }

    /**
     * Get rates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRates()
    {
        return $this->rates;
    }
}
