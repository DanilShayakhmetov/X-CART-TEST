<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Order tracking number
 *
 * @Entity
 * @Table (name="order_tracking_number")
 */
class OrderTrackingNumber extends \XLite\Model\AEntity
{
    /**
     * Order tracking number unique id
     *
     * @var mixed
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $tracking_id;

    /**
     * Value
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $value;

    /**
     * Order tracking creation date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $creationDate;

    /**
     * Relation to a order entity
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="trackingNumbers", fetch="LAZY")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;


    /**
     * Get tracking_id
     *
     * @return integer 
     */
    public function getTrackingId()
    {
        return $this->tracking_id;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return OrderTrackingNumber
     */
    public function setValue($value)
    {
        if ($this->isChanged($value)) {
            $this->setCreationDate();
        };
        
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     * @return OrderTrackingNumber
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set creation date
     *
     * @return OrderTrackingNumber
     */
    public function setCreationDate()
    {
        $this->creationDate = time();
        return $this;
    }

    /**
     * Get creation date
     *
     * @return creationDate
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Checks if the tracking number is changed
     *
     * @param string $value
     * @return boolean
     */
    private function isChanged($value)
    {
        return $this->value !== $value;
    }
}
