<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model;

/**
 * The OrderReviewKey model class
 *
 * @Entity
 * @Table  (name="order_review_keys",
 *     indexes={
 *         @Index (name="keyValue", columns={"keyValue"}),
 *         @Index (name="addedDate", columns={"addedDate"}),
 *         @Index (name="sentDate", columns={"sentDate"})
 *     }
 * )
 */
class OrderReviewKey extends \XLite\Model\AEntity
{
    /**
     * Unique key ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Key value
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=32)
     */
    protected $keyValue = '';

    /**
     * Date when key was created (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $addedDate = 0;

    /**
     * Date when key was sent (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $sentDate = 0;

    /**
     * Date when customer first time clicked by link with review key (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $firstClickDate = 0;

    /**
     * Relation to a profile entity (who adds review)
     *
     * @var \XLite\Model\Order
     *
     * @OneToOne  (targetEntity="XLite\Model\Order", inversedBy="reviewKey")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Relation to a profile entity (who adds review)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany  (targetEntity="XLite\Module\XC\Reviews\Model\Review", mappedBy="reviewKey")
     * @JoinColumn (name="review_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $reviews;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return true if review key corresponds to specified product
     *
     * @param \XLite\Model\Product $product Product model object
     *
     * @return boolean
     */
    public function isValidForProduct($product)
    {
        $result = false;

        if ($product && ($order = $this->getOrder())) {
            foreach ($order->getItems() as $item) {
                if ($item->getProduct()->getProductId() == $product->getProductId()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    // {{{ Default getters & setters

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
     * Set keyValue
     *
     * @param string $value
     * @return $this
     */
    public function setKeyValue($value)
    {
        $this->keyValue = $value;
        return $this;
    }

    /**
     * Get keyValue
     *
     * @return string
     */
    public function getKeyValue()
    {
        return $this->keyValue;
    }

    /**
     * Set addedDate
     *
     * @param integer $value
     * @return $this
     */
    public function setAddedDate($value)
    {
        $this->addedDate = $value;
        return $this;
    }

    /**
     * Get addedDate
     *
     * @return integer 
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

    /**
     * Set sentDate
     *
     * @param integer $value
     * @return $this
     */
    public function setSentDate($value)
    {
        $this->sentDate = $value;
        return $this;
    }

    /**
     * Get sentDate
     *
     * @return integer 
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set firstClickDate
     *
     * @param integer $value
     * @return $this
     */
    public function setFirstClickDate($value)
    {
        $this->firstClickDate = $value;
        return $this;
    }

    /**
     * Get firstClickDate
     *
     * @return integer 
     */
    public function getFirstClickDate()
    {
        return $this->firstClickDate;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $value
     * @return $this
     */
    public function setOrder($value)
    {
        $this->order = $value;
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
     * Add review
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $value
     * @return $this
     */
    public function addReviews($value)
    {
        $this->reviews[] = $value;
        return $this;
    }

    /**
     * Get reviews
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    public function getReviews()
    {
        return $this->reviews;
    }
}
