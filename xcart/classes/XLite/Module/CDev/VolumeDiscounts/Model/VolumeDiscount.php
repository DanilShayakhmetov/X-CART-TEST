<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Model;

use XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount;
use XLite\View\FormField\Input\PriceOrPercent;
use XLite\View\FormField\Select\AbsoluteOrPercent;

/**
 * Volume discount model
 *
 * @Entity
 * @Table  (name="volume_discounts",
 *      indexes={
 *          @Index (name="date_range", columns={"dateRangeBegin","dateRangeEnd"}),
 *          @Index (name="subtotal", columns={"subtotalRangeBegin"}),
 *      }
 * )
 */
class VolumeDiscount extends \XLite\Model\AEntity
{
    const TYPE_PERCENT  = '%';
    const TYPE_ABSOLUTE = '$';


    /**
     * Discount unique ID
     *
     * @var   integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Value
     *
     * @var   float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Type
     *
     * @var   string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_PERCENT;

    /**
     * Subtotal range (begin)
     *
     * @var   float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $subtotalRangeBegin = 0;

    /**
     * Membership
     *
     * @var   \XLite\Model\Membership
     *
     * @ManyToOne (targetEntity="XLite\Model\Membership")
     * @JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $membership;

    /**
     * Zones
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Model\Zone", inversedBy="volume_discounts")
     * @JoinTable (name="zones_volume_discounts",
     *      joinColumns={@JoinColumn (name="volume_discount_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")}
     * )
     */
    protected $zones;

    /**
     * Date range (begin)
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $dateRangeBegin = 0;

    /**
     * Date range (end)
     *
     * @var   integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $dateRangeEnd = 0;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->zones = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Returns handling fee
     *
     * @return array
     */
    public function getDiscount()
    {
        return [
            PriceOrPercent::PRICE_VALUE => $this->getValue(),
            PriceOrPercent::TYPE_VALUE  => $this->getType() === static::TYPE_PERCENT
                ? AbsoluteOrPercent::TYPE_PERCENT
                : AbsoluteOrPercent::TYPE_ABSOLUTE
        ];
    }

    /**
     * Set Discount
     *
     * @param array $Discount
     * @return Method
     */
    public function setDiscount($Discount)
    {
        $this->setValue(
            isset($Discount[PriceOrPercent::PRICE_VALUE])
                ? $Discount[PriceOrPercent::PRICE_VALUE]
                : 0
        );

        $this->setType(
            isset($Discount[PriceOrPercent::TYPE_VALUE])
            && $Discount[PriceOrPercent::TYPE_VALUE] === AbsoluteOrPercent::TYPE_PERCENT
                ? static::TYPE_PERCENT
                : static::TYPE_ABSOLUTE
        );

        return $this;
    }

    /**
     * Check - discount is absolute or not
     *
     * @return boolean
     */
    public function isAbsolute()
    {
        return static::TYPE_ABSOLUTE == $this->getType();
    }

    /**
     * Get discount amount
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getAmount(\XLite\Model\Order $order)
    {
        $subTotal = $order->getSubtotal();

        /** @var \XLite\Model\Order\Surcharge $surcharge */
        foreach ($order->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT) as $surcharge) {
            if (
                $surcharge->getAvailable()
                && !$surcharge->getInclude()
                && $surcharge->getCode() !== Discount::MODIFIER_CODE
            ) {
                $subTotal += $order->getCurrency()->roundValue($surcharge->getValue());
            }
        }

        $discount = $this->isAbsolute()
            ? $this->getValue()
            : ($subTotal * $this->getValue() / 100);

        return min($discount, $subTotal);
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
     * Set value
     *
     * @param float $value
     * @return VolumeDiscount
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return VolumeDiscount
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set subtotalRangeBegin
     *
     * @param float $subtotalRangeBegin
     * @return VolumeDiscount
     */
    public function setSubtotalRangeBegin($subtotalRangeBegin)
    {
        $this->subtotalRangeBegin = $subtotalRangeBegin;
        return $this;
    }

    /**
     * Get subtotalRangeBegin
     *
     * @return float
     */
    public function getSubtotalRangeBegin()
    {
        return $this->subtotalRangeBegin;
    }

    /**
     * Set dateRangeBegin
     *
     * @param integer $dateRangeBegin
     * @return VolumeDiscount
     */
    public function setDateRangeBegin($dateRangeBegin)
    {
        $this->dateRangeBegin = $dateRangeBegin;
        return $this;
    }

    /**
     * Get dateRangeBegin
     *
     * @return integer
     */
    public function getDateRangeBegin()
    {
        return $this->dateRangeBegin;
    }

    /**
     * Set dateRangeEnd
     *
     * @param integer $dateRangeEnd
     * @return VolumeDiscount
     */
    public function setDateRangeEnd($dateRangeEnd)
    {
        $this->dateRangeEnd = $dateRangeEnd;
        return $this;
    }

    /**
     * Get dateRangeEnd
     *
     * @return integer
     */
    public function getDateRangeEnd()
    {
        return $this->dateRangeEnd;
    }

    /**
     * Get real dateRangeEnd ()
     *
     * @return integer
     */
    public function getRealDateRangeEnd()
    {
        return $this->dateRangeEnd == 0
            ? PHP_INT_MAX
            : $this->dateRangeEnd;
    }

    /**
     * Add zone
     *
     * @param \XLite\Model\Zone $zone
     * @return VolumeDiscount
     */
    public function addZone(\XLite\Model\Zone $zone)
    {
        $this->zones[] = $zone;
        return $this;
    }

    /**
     * Get zones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Clear zones
     */
    public function clearZones()
    {
        if ($this->getZones()) {
            $this->getZones()->clear();
        }
    }

    /**
     * Set membership
     *
     * @param string $membershipId
     * @return VolumeDiscount
     */
    public function setMembership(string $membershipId)
    {
        $membership = \XLite\Core\Database::getRepo('\XLite\Model\Membership')->find($membershipId);
        $this->membership = $membership;
        return $this;
    }

    /**
     * Get membership
     *
     * @return \XLite\Model\Membership
     */
    public function getMembership()
    {
        return $this->membership;
    }

    /**
     * Check - volume discount is expired or not
     *
     * @return boolean
     */
    public function isExpired()
    {
        return 0 < $this->getDateRangeEnd() && $this->getDateRangeEnd() < \XLite\Core\Converter::time();
    }
}
