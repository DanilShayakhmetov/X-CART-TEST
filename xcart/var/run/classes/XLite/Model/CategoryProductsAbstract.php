<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Category
 *
 *  Entity
 *  Table (name="category_products",
 *      uniqueConstraints={
 *           UniqueConstraint (name="pair", columns={"category_id","product_id"})
 *      },
 *      indexes={
 *           Index (name="orderby", columns={"orderby"}),
 *           Index (name="orderbyInProduct", columns={"orderbyInProduct"})
 *      }
 * )
 */
abstract class CategoryProductsAbstract extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product position in the category
     *
     * @var integer
     *
     * @Column (type="integer", length=11, nullable=false)
     */
    protected $orderby = 0;

    /**
     * Category position in the product
     *
     * @var integer
     *
     * @Column (type="integer", length=11, nullable=false)
     */
    protected $orderbyInProduct = 0;

    /**
     * Relation to a category entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="categoryProducts")
     * @JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * Relation to a product entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="categoryProducts")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

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
     * Set orderby
     *
     * @param integer $orderby
     * @return CategoryProducts
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer 
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set category
     *
     * @param \XLite\Model\Category $category
     * @return CategoryProducts
     */
    public function setCategory(\XLite\Model\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \XLite\Model\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return CategoryProducts
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getOrderbyInProduct()
    {
        return $this->orderbyInProduct;
    }

    /**
     * @param int $orderbyInProduct
     */
    public function setOrderbyInProduct($orderbyInProduct)
    {
        $this->orderbyInProduct = $orderbyInProduct;
    }
}
