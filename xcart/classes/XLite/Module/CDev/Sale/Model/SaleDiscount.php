<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model;

/**
 * Sale
 *
 * @Entity
 * @Table  (name="sale_discounts")
 *
 * @HasLifecycleCallbacks
 */
class SaleDiscount extends \XLite\Model\Base\Catalog
{
    /**
     * Product unique ID
     *
     * @var   integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Enabled status
     *
     * @var   boolean
     *
     * @Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Value
     *
     * @var   float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

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
     * Flag: Sale products is shown in separate section
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $showInSeparateSection = false;

    /**
     * Meta description type
     *
     * @var string
     *
     * @Column (type="string", length=1)
     */
    protected $metaDescType = 'A';

    /**
     * Flag: Sale is used for specific products or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $specificProducts = false;

    /**
     * Product classes
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Model\ProductClass", inversedBy="saleDiscounts")
     * @JoinTable (name="product_class_sale_discounts",
     *      joinColumns={@JoinColumn (name="sale_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="class_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $productClasses;

    /**
     * Memberships
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Model\Membership", inversedBy="saleDiscounts")
     * @JoinTable (name="membership_sale_discounts",
     *      joinColumns={@JoinColumn (name="sale_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")}
     * )
     */
    protected $memberships;

    /**
     * Categories
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Model\Category", inversedBy="saleDiscounts")
     * @JoinTable (name="category_sale_discounts",
     *      joinColumns={@JoinColumn (name="sale_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")}
     * )
     */
    protected $categories;

    /**
     * Sale products
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\Sale\Model\SaleDiscountProduct", mappedBy="saleDiscount")
     */
    protected $saleDiscountProducts;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="sale_discount", cascade={"all"})
     * @OrderBy   ({"id" = "ASC"})
     */
    protected $cleanURLs;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->productClasses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->saleDiscountProducts   = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check discount activity
     *
     * @return boolean
     */
    public function isActive()
    {
        if (!$this->getEnabled()) {
            return false;
        }

        if (!$this->isStarted()) {
            return false;

        }
        if ($this->isExpired()) {
            return false;
        }

        return true;
    }

    /**
     * Check - discount is started
     *
     * @return boolean
     */
    public function isStarted()
    {
        return 0 === $this->getDateRangeBegin() || $this->getDateRangeBegin() < \XLite\Core\Converter::time();
    }

    /**
     * Check - discount is expired or not
     *
     * @return boolean
     */
    public function isExpired()
    {
        return 0 < $this->getDateRangeEnd() && $this->getDateRangeEnd() < \XLite\Core\Converter::time();
    }

    /**
     * @param \XLite\Model\Product $product
     * @return bool
     */
    public function isApplicableForProduct(\XLite\Model\Product $product)
    {
        if ($this->getSpecificProducts()) {
            if (!$this->checkSpecificProduct($product)) {
                return false;
            }

        } else {
            if (!$this->checkCategoryForProduct($product)) {
                return false;
            }
            if (!$this->checkProductClassForProduct($product)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \XLite\Model\Category $category
     * @return bool
     */
    public function isApplicableForCategory(\XLite\Model\Category $category)
    {
        if ($this->getSpecificProducts()) {
            return false;
        }

        if ($this->getCategories()->count()) {
             return $this->getCategories()->contains($category);
        }

        return false;
    }

    /**
     * @param \XLite\Model\Product $product
     * @return bool
     */
    protected function checkCategoryForProduct(\XLite\Model\Product $product)
    {
        if ($this->getCategories()->count()) {
            foreach ($product->getCategories() as $category) {
                if ($this->getCategories()->contains($category)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * @param \XLite\Model\Product $product
     * @return bool
     */
    protected function checkProductClassForProduct(\XLite\Model\Product $product)
    {
        if ($this->getProductClasses()->count()) {
            return $this->getProductClasses()->contains($product->getProductClass());
        }

        return true;
    }

    /**
     * @param \XLite\Model\Product $product
     * @return bool
     */
    protected function checkSpecificProduct(\XLite\Model\Product $product)
    {
        return in_array($product->getProductId(), $this->getApplicableProductIds());
    }

    /**
     * @param \XLite\Model\Profile $profile
     * @return bool
     */
    public function isApplicableForProfile(\XLite\Model\Profile $profile)
    {
        if (!$this->checkMembershipForProfile($profile)) {
            return false;
        }

        return true;
    }

    /**
     * @param \XLite\Model\Profile $profile
     * @return bool
     */
    protected function checkMembershipForProfile(\XLite\Model\Profile $profile)
    {
        if ($this->getMemberships()->count()) {
            return $this->getMemberships()->contains($profile->getMembership());
        }

        return true;
    }

    /**
     * Returns meta description
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return 'A' === $this->getMetaDescType()
            ? static::postprocessMetaDescription($this->getName())
            : $this->getSoftTranslation()->getMetaDesc();
    }

    /**
     * Returns meta description type
     *
     * @return string
     */
    public function getMetaDescType()
    {
        $result = $this->metaDescType;

        if (!$result) {
            $metaDescPresent = array_reduce($this->getTranslations()->toArray(), function ($carry, $item) {
                return $carry ?: (bool) $item->getMetaDesc();
            }, false);

            $result = $metaDescPresent ? 'C' : 'A';
        }

        return $result;
    }

    /**
     * Set metaDescType
     *
     * @param string $metaDescType
     */
    public function setMetaDescType($metaDescType)
    {
        $this->metaDescType = $metaDescType;
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
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (boolean)$enabled;
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
     * Set value
     *
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * Set dateRangeBegin
     *
     * @param integer $dateRangeBegin
     */
    public function setDateRangeBegin($dateRangeBegin)
    {
        $this->dateRangeBegin = $dateRangeBegin;
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
     */
    public function setDateRangeEnd($dateRangeEnd)
    {
        $this->dateRangeEnd = $dateRangeEnd;
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
     * Set showInSeparateSection
     *
     * @param boolean $showInSeparateSection
     */
    public function setShowInSeparateSection($showInSeparateSection)
    {
        $this->showInSeparateSection = $showInSeparateSection;
    }

    /**
     * Get showInSeparateSection
     *
     * @return boolean
     */
    public function getShowInSeparateSection()
    {
        return $this->showInSeparateSection;
    }

    /**
     * Set specificProducts
     *
     * @param boolean $specificProducts
     */
    public function setSpecificProducts($specificProducts)
    {
        $this->specificProducts = $specificProducts;
    }

    /**
     * Get specificProducts
     *
     * @return boolean
     */
    public function getSpecificProducts()
    {
        return $this->specificProducts;
    }

    /**
     * Add productClasses
     *
     * @param \XLite\Model\ProductClass $productClasses
     */
    public function addProductClasses(\XLite\Model\ProductClass $productClasses)
    {
        $this->productClasses[] = $productClasses;
    }

    /**
     * Get productClasses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductClasses()
    {
        return $this->productClasses;
    }

    /**
     * Clear product classes
     */
    public function clearProductClasses()
    {
        foreach ($this->getProductClasses()->getKeys() as $key) {
            $this->getProductClasses()->remove($key);
        }
    }

    /**
     * Add memberships
     *
     * @param \XLite\Model\Membership $memberships
     */
    public function addMemberships(\XLite\Model\Membership $memberships)
    {
        $this->memberships[] = $memberships;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Clear memberships
     */
    public function clearMemberships()
    {
        foreach ($this->getMemberships()->getKeys() as $key) {
            $this->getMemberships()->remove($key);
        }
    }

    /**
     * Add categories
     *
     * @param \XLite\Model\Category $categories
     */
    public function addCategories(\XLite\Model\Category $categories)
    {
        $this->getCategories()->add($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Clear categories
     */
    public function clearCategories()
    {
        foreach ($this->getCategories()->getKeys() as $key) {
            $this->getCategories()->remove($key);
        }
    }

    /**
     * Add sale discount products
     *
     * @param \XLite\Module\CDev\Sale\Model\SaleDiscountProduct $saleDiscountProduct
     */
    public function addSaleDiscountProducts(\XLite\Module\CDev\Sale\Model\SaleDiscountProduct $saleDiscountProduct)
    {
        $this->saleDiscountProducts[] = $saleDiscountProduct;
    }

    /**
     * Get product ids if sale discount is specificProducts
     *
     * @return array
     */
    public function getApplicableProductIds()
    {
        $ids = [];
        if ($this->isPersistent() && $this->getSpecificProducts()) {
            $ids = \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscountProduct')
                ->getSaleDiscountProductIds($this->getId());
        }

        return $ids;
    }

    /**
     * Get sale products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSaleDiscountProducts()
    {
        return $this->saleDiscountProducts;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCleanURLs()
    {
        return $this->cleanURLs;
    }

    /**
     * Add cleanURLs
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     * @return SaleDiscount
     */
    public function addCleanURLs(\XLite\Model\CleanURL $cleanURLs)
    {
        $this->cleanURLs[] = $cleanURLs;
        return $this;
    }
}
