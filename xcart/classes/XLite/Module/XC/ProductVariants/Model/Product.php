<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model;

/**
 * Product
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product variants
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\ProductVariants\Model\ProductVariant", mappedBy="product", cascade={"all"})
     */
    protected $variants;

    /**
     * Product variants attributes
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany (targetEntity="XLite\Model\Attribute", inversedBy="variantsProducts")
     * @JoinTable (
     *      name="product_variants_attributes",
     *      joinColumns={@JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")},
     *      inverseJoinColumns={@JoinColumn (name="attribute_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $variantsAttributes;

    /**
     * Default variant
     *
     * @var   \XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    protected $defaultVariant;

    /**
     * Cached variants count
     */
    protected $variantsCount;

    /**
     * Cached variants state
     */
    protected $hasVariants;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->variantsAttributes = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get variant by attribute values ids
     *
     * @param array $ids Ids
     * @param boolean $singleVariant Return single variants (true) or array of all matched variants (false) OPTIONAL
     *
     * @return mixed
     */
    public function getVariantByAttributeValuesIds(array $ids, $singleVariant = true)
    {
        $result = $singleVariant ? null : [];

        foreach ($this->getVariants() as $variant) {
            $match = true;
            foreach ($variant->getValues() as $v) {
                $match = isset($ids[$v->getAttribute()->getId()])
                         && $ids[$v->getAttribute()->getId()] == $v->getId();
                if (!$match) {
                    break;
                }
            }
            if ($match) {
                if ($singleVariant) {
                    $result = $variant;
                    break;

                } else {
                    $result[] = $variant;
                }
            }
        }

        return $result;
    }

    /**
     * Get variant by any count of attribute values ids
     *
     * @param array $ids Ids [attribute_id => value_id]
     * @param boolean $singleVariant Return single variants (true) or array of all matched variants (false) OPTIONAL
     *
     * @return mixed
     */
    public function getVariantByAnyAttributeValuesIds(array $ids, $singleVariant = true)
    {
        $result = $singleVariant ? null : [];

        $variant = $this->getVariants() ? $this->getVariants()->first() : null;

        if (!$variant) {
            return null;
        }

        $variantAttributes = array_map(function ($value) {
            return $value->getAttribute()->getId();
        }, $variant->getValues());

        $filteredIds = [];
        foreach ($ids as $k => $v) {
            if (in_array($k, $variantAttributes, true)) {
                $filteredIds[$k] = $v;
            }
        }
        $ids = $filteredIds;

        if (empty($ids)) {
            return $singleVariant
                ? $variant
                : $this->getVariants();
        }

        foreach ($this->getVariants() as $variant) {
            $temporaryIds = $ids;
            foreach ($variant->getValues() as $v) {
                if (empty($temporaryIds)) {
                    break;
                }

                $match = isset($temporaryIds[$v->getAttribute()->getId()])
                         && $temporaryIds[$v->getAttribute()->getId()] == $v->getId();

                if ($match) {
                    unset($temporaryIds[$v->getAttribute()->getId()]);
                }
            }
            if (!empty($match) && empty($temporaryIds)) {
                if ($singleVariant) {
                    $result = $variant;
                    break;

                } else {
                    $result[] = $variant;
                }
            }
        }

        return $result;
    }

    /**
     * Get variant by attribute values
     *
     * @param integer[]|\XLite\Model\AttributeValue\AAttributeValue[] $attributeValues Attribute values
     *
     * @return integer[]
     */
    public function getVariantByAttributeValues($attributeValues)
    {
        $result = [];

        foreach ($attributeValues as $attributeId => $valueId) {
            if (is_scalar($valueId)) {
                $result[(int)$attributeId] = (int)$valueId;

            } elseif ($valueId instanceof \XLite\Model\AttributeValue\AAttributeValue) {
                $result[$valueId->getAttribute()->getId()] = $valueId->getId();
            }
        }

        return $this->getVariantByAttributeValuesIds($result);
    }

    /**
     * Get quick minimal data price
     *
     * @return float
     */
    public function getQuickDataMinPrice()
    {
        if ($this->hasVariants()) {
            $variants = $this->getVariants();
            $price = $variants->first()
                ? $variants->first()->getQuickDataPrice()
                : $this->getClearPrice();
            foreach ($variants as $variant) {
                if ($variant->getQuickDataPrice() < $price) {
                    $price = $variant->getQuickDataPrice();
                }
            }

            return $price;
        } else {
            return $this->getQuickDataPrice();
        }
    }

    /**
     * Get quick data maximal price
     *
     * @return float
     */
    public function getQuickDataMaxPrice()
    {
        if ($this->hasVariants()) {
            $variants = $this->getVariants();
            $price = $variants->first()
                ? $variants->first()->getQuickDataPrice()
                : $this->getClearPrice();
            foreach ($variants as $variant) {
                if ($variant->getQuickDataPrice() > $price) {
                    $price = $variant->getQuickDataPrice();
                }
            }

            foreach ($this->prepareAttributeValues() as $av) {
                if (is_object($av)) {
                    $price += $av->getAbsoluteValue('price');
                }
            }

            return $price;
        } else {
            return $this->getQuickDataPrice();
        }
    }

    /**
     * Get default variant
     *
     * @return mixed
     */
    public function getDefaultVariant()
    {
        if ($this->hasVariants()) {
            if (null === $this->defaultVariant) {
                $this->defineDefaultVariant();
            }
            if (!\XLite::isAdminZone() && $this->defaultVariant->isOutOfStock()) {
                $this->setFirstAvailableVariantAsDefault();
            }
        }

        return $this->defaultVariant ?: null;
    }

    /**
     * Set first variant as default
     *
     * @return void
     */
    public function setFirstVariantAsDefault()
    {
        $this->defaultVariant = $this->variants->first();
        $this->defaultVariant->setDefaultValue(true);
        \XLite\Core\Database::getEM()->flush($this->defaultVariant);
    }

    /**
     * Set first available variant as default
     *
     * @return void
     */
    public function setFirstAvailableVariantAsDefault()
    {
        foreach ($this->getVariants() as $variant) {
            if (!$variant->isOutOfStock()) {
                $this->defaultVariant = $variant;
                return;
            }
        }
    }

    /**
     * Assign default variant
     *
     * @return mixed
     */
    public function assignDefaultVariant()
    {
        $defaultVariant = $this->getDefaultVariant();

        if ($defaultVariant && !$defaultVariant->getDefaultValue()) {
            $defaultVariant->setDefaultValue(true);
            \XLite\Core\Database::getEM()->flush($defaultVariant);
        }

        return $defaultVariant;
    }

    /**
     * Get image
     *
     * @return \XLite\Model\Image\Product\Image
     */
    public function getImage()
    {
        return $this->isUseVariantImage()
            ? $this->getDefaultVariant()->getImage()
            : $this->getProductImage();
    }

    /**
     * Get image
     *
     * @return \XLite\Model\Image\Product\Image
     */
    public function getProductImage()
    {
        return parent::getImage();
    }

    /**
     * Return true if variant image should be used instead of default product image
     *
     * @return boolean
     */
    protected function isUseVariantImage()
    {
        $result = !\XLite::isAdminZone()
                  && $this->getDefaultVariant()
                  && $this->getDefaultVariant()->getImage();

        if (
            $result
            && \XLite\Core\Config::getInstance()->General->force_choose_product_options
            && $this->getProductImage()
        ) {
            return false;
        }

        return $result;
    }

    /**
     * Get public images
     *
     * @return array
     */
    public function getPublicImages()
    {
        $list = parent::getPublicImages();

        if ($this->isUseVariantImage()) {
            array_unshift($list, $this->getDefaultVariant()->getImage());
        }

        return $list;
    }

    /**
     * Get clear price
     *
     * @return float
     */
    public function getClearPrice()
    {
        return $this->getDefaultVariant()
            ? $this->getDefaultVariant()->getClearPrice()
            : parent::getClearPrice();
    }


    /**
     * Get variant
     *
     * @param mixied $attributeValues Attribute values OPTIONAL
     *
     * @return mixed
     */
    public function getVariant($attributeValues = null)
    {
        return $attributeValues
            ? $this->getVariantByAttributeValues($attributeValues)
            : $this->getDefaultVariant();
    }

    /**
     * Check product must have variants or not
     *
     * @return boolean
     */
    public function mustHaveVariants()
    {
        return 0 < $this->getVariantsAttributes()->count();
    }

    /**
     * Return variants count
     *
     * @return integer
     */
    public function getVariantsCount()
    {
        if (null === $this->variantsCount && $this->isPersistent()) {
            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant');
            $this->variantsCount = $repo->getVariantsCountByProduct($this);
        }

        return $this->variantsCount;
    }

    /**
     * Check product has variants or not
     *
     * @return boolean
     */
    public function hasVariants()
    {
        if ($this->hasVariants === null) {
            if ($this->isPersistent()) {
                $repo          = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant');
                $variantsCount = $repo->getVariantsCountByProduct($this);

                $this->hasVariants = 0 < $variantsCount;
            } else {
                $this->hasVariants = false;
            }
        }

        return $this->hasVariants;
    }

    /**
     * Return product amount available to add to cart
     *
     * @return integer
     */
    public function getAvailableAmount()
    {
        return $this->hasVariants()
            ? $this->getVariant()->getAvailableAmount()
            : parent::getAvailableAmount();
    }

    /**
     * Alias: is product in stock or not
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        return $this->hasVariants()
            ? $this->getVariant()->isOutOfStock()
            : parent::isOutOfStock();
    }


    public function getItemsInCart()
    {
        return $this->hasVariants()
            ? $this->getVariant()->getItemsInCart()
            : parent::getItemsInCart();
    }

    /**
     * Alias: is all product items in cart
     *
     * @return boolean
     */
    public function isAllStockInCart()
    {
        return $this->hasVariants()
            ? $this->getVariant()->isAllStockInCart()
            : parent::isAllStockInCart();
    }

    /**
     * Get all possible variants count
     *
     * @return integer
     */
    public function getAllPossibleVariantsCount()
    {
        $result = 1;

        foreach ($this->getVariantsAttributes() as $a) {
            $result *= count($a->getAttributeValue($this));
        }

        return $result;
    }

    /**
     * Get variants attribute ids
     *
     * @return array
     */
    public function getVariantsAttributeIds()
    {
        $variantsAttributeIds = [];

        foreach ($this->getVariantsAttributes() as $va) {
            $variantsAttributeIds[$va->getId()] = $va->getId();
        }

        return $variantsAttributeIds;
    }

    /**
     * Return true if product has incomplete variants list
     *
     * @return boolean
     */
    public function hasIncompleteVariantsList()
    {
        $allVariantsCount = $this->getAllPossibleVariantsCount();

        return 0 < $allVariantsCount && count($this->variants) < $allVariantsCount;
    }

    /**
     * Get clear weight
     *
     * @return float
     */
    public function getClearWeight()
    {
        return $this->getDefaultVariant()
            ? $this->getDefaultVariant()->getClearWeight()
            : parent::getClearWeight();
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->getVariant()
            ? (
                \XLite\Core\Config::getInstance()->General->force_choose_product_options === ''
                && $this->getVariant()->isShowStockWarning()
            )
            : parent::isShowStockWarning();
    }

    /**
     * Check variants attributes
     *
     * @return void
     */
    public function checkVariantsAttributes()
    {
        if (0 === count($this->getVariants())) {
            foreach ($this->getVariantsAttributes() as $attribute) {
                $attribute->getVariantsProducts()->removeElement($this);
                $this->getVariantsAttributes()->removeElement($attribute);
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Check variants
     *
     * @return void
     */
    public function checkVariants()
    {
        $changed = false;

        foreach ($this->getVariantsAttributes() as $va) {
            if (!$va->isMultiple($this)) {
                \XLite\Core\Database::getRepo('\XLite\Module\XC\ProductVariants\Model\ProductVariant')->deleteInBatch(
                    $this->getVariantsCollection()->toArray()
                );
                $this->getVariantsCollection()->clear();
                $this->getVariantsAttributes()->clear();

                $changed = true;
                break;
            }
        }

        if (0 < $this->getVariantsCollection()->count()) {
            if (0 === $this->getVariantsAttributes()->count()) {
                \XLite\Core\Database::getRepo('\XLite\Module\XC\ProductVariants\Model\ProductVariant')->deleteInBatch(
                    $this->getVariantsCollection()->toArray()
                );

                $this->getVariantsCollection()->clear();
                $changed = true;

            } else {
                foreach ($this->getVariantsAttributes() as $a) {
                    $variantsAttributes[$a->getId()] = $a->getId();
                }

                foreach ($this->getVariantsCollection() as $variant) {
                    $toAdd = $variantsAttributes;

                    foreach ($variant->getValues() as $v) {
                        $attribute = $v->getAttribute();
                        if (isset($toAdd[$attribute->getId()])) {
                            unset($toAdd[$attribute->getId()]);

                        } else {
                            $method = 'getAttributeValue' . $attribute->getType();
                            $variant->$method()->removeElement($v);
                            $v->getVariants()->removeElement($variant);
                            $changed = true;
                        }
                    }

                    if ($toAdd) {
                        $attributes = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->findByIds($toAdd);
                        foreach ($attributes as $a) {
                            $aValue = $a->getAttributeValue($this);
                            $method = 'addAttributeValue' . $a->getType();
                            $attributeValue = array_shift($aValue);
                            $variant->$method($attributeValue);
                            $attributeValue->addVariants($variant);
                            $changed = true;
                        }
                    }
                }

                foreach ($this->getVariantsCollection() as $v) {
                    if (!isset($checked[$v->getId()])) {
                        if ($v->getValues()) {
                            $hash = $v->getValuesHash();
                            foreach ($this->getVariantsCollection() as $v2) {
                                if ($v->getId() != $v2->getId()
                                    && $v2->getValues()
                                    && !isset($checked[$v2->getId()])
                                ) {
                                    if ($v2->getValuesHash() === $hash) {
                                        $changed = true;
                                        \XLite\Core\Database::getEM()->remove($v2);
                                        $checked[$v2->getId()] = true;
                                    }
                                }
                            }

                        } else {
                            $changed = true;
                            \XLite\Core\Database::getEM()->remove($v);
                        }
                        $checked[$v->getId()] = true;
                    }
                }
            }
        }

        if ($changed) {
            $this->updateQuickData();
            \XLite\Core\Database::getEM()->flush();
        }

        $this->assignDefaultVariant();
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        if ($this->mustHaveVariants()) {
            $attrs = [];
            foreach ($this->getVariantsAttributes() as $a) {
                $attribute = null;

                if ($a->getProduct()) {
                    $cnd = new \XLite\Core\CommonCell();
                    $cnd->product = $newProduct;
                    $cnd->name = $a->getName();
                    $cnd->type = $a->getType();

                    $attributes = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->search($cnd);
                    if ($attributes && is_array($attributes)) {
                        $attribute = array_pop($attributes);
                    } else {
                        $attribute = $a;
                    }

                } else {
                    $attribute = $a;
                }

                if ($attribute) {
                    $attrs[$a->getId()] = $attribute;

                    $newProduct->addVariantsAttributes($attribute);
                    $attribute->addVariantsProduct($newProduct);
                }
            }

            foreach ($this->getVariants() as $variant) {
                $newVariant = $variant->cloneEntity();
                $newVariant->setProduct($newProduct);
                $newProduct->addVariants($newVariant);
                \XLite\Core\Database::getEM()->persist($newVariant);

                foreach ($variant->getValues() as $av) {
                    $attribute = $attrs[$av->getAttribute()->getId()];
                    foreach ($attribute->getAttributeValue($newProduct) as $v) {
                        if ($v->asString() === $av->asString()) {
                            $method = 'addAttributeValue' . $attribute->getType();
                            $newVariant->$method($v);
                            $v->addVariants($newVariant);
                        }
                    }
                }
            }

            $newProduct->update();
        }

        return $newProduct;
    }

    /**
     * Preprocess change product class
     *
     * @return void
     */
    protected function preprocessChangeProductClass()
    {
        parent::preprocessChangeProductClass();

        $changed = false;

        foreach ($this->getVariantsAttributes() as $va) {
            if ($va->getProductClass()
                && $va->getProductClass() == $this->productClass->getId()
            ) {
                $this->getVariantsAttributes()->removeElement($va);
                $va->getVariantsProducts()->removeElement($this);
                $changed = true;
            }
        }

        if ($changed) {
            $this->checkVariants();
        }
    }

    /**
     * Add variants
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $variants
     * @return Product
     */
    public function addVariants(\XLite\Module\XC\ProductVariants\Model\ProductVariant $variants)
    {
        $this->variants[] = $variants;
        return $this;
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVariants()
    {
        return $this->getVariantsCollection()->filter(function ($variant) {
            return $variant && $variant->getValues();
        });
    }

    /**
     * Get all variants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVariantsCollection()
    {
        return $this->variants;
    }

    /**
     * Add variantsAttributes
     *
     * @param \XLite\Module\XC\ProductVariants\Model\Attribute $variantsAttributes
     * @return Product
     */
    public function addVariantsAttributes(\XLite\Model\Attribute $variantsAttributes)
    {
        $this->variantsAttributes[] = $variantsAttributes;
        $variantsAttributes->addVariantsProducts($this);

        return $this;
    }

    /**
     * Get variantsAttributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVariantsAttributes()
    {
        return $this->variantsAttributes;
    }

    /**
     * Check if product price in list should be displayed as range
     *
     * @return bool
     */
    public function isDisplayPriceAsRange()
    {
        return \XLite\Module\XC\ProductVariants\Main::isDisplayPriceAsRange() && $this->getVariantsCount() > 1;
    }

    /**
     * Define default variant
     *
     * @return void
     */
    protected function defineDefaultVariant()
    {
        $defaultVariant = null;

        $repo = \XLite\Core\Database::getRepo('\XLite\Module\XC\ProductVariants\Model\ProductVariant');
        $defaultVariant = $repo->findOneBy(
            array(
                'product'      => $this,
                'defaultValue' => true,
            )
        );

        if ($defaultVariant) {
            $this->defaultVariant = $defaultVariant;
        } else {
            $this->setFirstVariantAsDefault();
        }
    }
}
