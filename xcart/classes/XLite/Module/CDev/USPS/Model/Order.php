<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model;

/**
 * Class represents an order
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * USPS Shipments
     *
     * @var \Doctrine\Common\Collections\Collection|\XLite\Module\CDev\USPS\Model\Shipment[]
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\USPS\Model\Shipment", mappedBy="order", cascade={"all"})
     */
    protected $uspsShipment;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->uspsShipment = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Shipment[]
     */
    public function getUspsShipment()
    {
        return $this->uspsShipment;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection|Shipment[] $uspsShipment
     */
    public function setUspsShipment($uspsShipment)
    {
        $this->uspsShipment = $uspsShipment;
    }
}
