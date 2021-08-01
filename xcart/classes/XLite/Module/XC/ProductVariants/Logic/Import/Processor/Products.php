<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Logic\Import\Processor;

use XLite\Core\Database;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    const VARIANT_PREFIX = 'variant';

    /**
     * Product variants
     *
     * @var array
     */
    protected $variants = [];

    /**
     * List of provided variantIds
     *
     * @var array
     */
    protected $variantIds = [];

    /**
     * Product variants attributes
     *
     * @var array
     */
    protected $variantsAttributes = [];

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns += [
            static::VARIANT_PREFIX . 'ID'      => [
                static::COLUMN_IS_MULTIROW => true,
            ],
            static::VARIANT_PREFIX . 'SKU'      => [
                static::COLUMN_IS_MULTIROW => true,
                static::COLUMN_LENGTH      => 32,
            ],
            static::VARIANT_PREFIX . 'Price'    => [
                static::COLUMN_IS_MULTIROW => true,
            ],
            static::VARIANT_PREFIX . 'Quantity' => [
                static::COLUMN_IS_MULTIROW => true,
            ],
            static::VARIANT_PREFIX . 'Weight'   => [
                static::COLUMN_IS_MULTIROW => true,
            ],
            static::VARIANT_PREFIX . 'Image'    => [
                static::COLUMN_IS_MULTIROW => true,
            ],
            static::VARIANT_PREFIX . 'ImageAlt' => [
                static::COLUMN_IS_MULTIROW => true,
                static::COLUMN_LENGTH      => 255,
            ],
            static::VARIANT_PREFIX . 'defaultValue' => [
                static::COLUMN_IS_MULTIROW => true,
                static::COLUMN_LENGTH      => 255,
            ],
        ];

        $columns += [
            'identity' => [
                static::COLUMN_IS_MULTICOLUMN  => true,
                static::COLUMN_IS_MULTIROW     => true,
                static::COLUMN_HEADER_DETECTOR => true,
            ],
        ];

        return $columns;
    }

    /**
     * Detect identity header(s)
     *
     * @param array $column Column info
     * @param array $row Header row
     *
     * @return array
     */
    protected function detectIdentityHeader(array $column, array $row)
    {
        $variantId = static::VARIANT_PREFIX . 'ID';
        return $this->detectHeaderByPattern("(sku|{$variantId})", $row);
    }

    // }}}

    // {{{ Verification
    protected function verifyData(array $data)
    {
        $this->prepareVariants($data, true);

        unset($data[static::VARIANT_PREFIX . 'ID']);

        return parent::verifyData($data);
    }

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages() + [
                'VARIANT-PRICE-FMT'       => 'Wrong variant price format',
                'VARIANT-QUANTITY-FMT'    => 'Wrong variant quantity format',
                'VARIANT-PRODUCT-SKU-FMT' => 'SKU is already assigned to variant',
                'VARIANT-WEIGHT-FMT'      => 'Wrong variant weight format',
                'VARIANT-IMAGE-FMT'       => 'The "{{value}}" image does not exist',
                'VARIANT-ATTRIBUTE-FMT'   => 'Variant attribute "{{column}}" cannot be empty',
                'VARIANT-PRODUCT-FMT'     => 'Variant id X is already assigned to another product variant',
                'VARIANT-SKU-FMT'         => 'Variant sku must be unique',
                'VARIANT-ID-MISMATCH'     => 'Couldn\'t identify a variant based on ID X being imported',
                'VARIANT-ID-CHANGED'      => 'variant ID X was replaced by ID Y generated automatically',
                'VARIANT-TYPE-FMT'        => 'Field type for the attribute "{{value}}" is TEXT AREA; this type cannot be used to configure multiple attribute values.',
            ];
    }

    /**
     * Verify identity
     *
     * @param mixed $values Value
     * @param array $column Column info
     */
    protected function verifyIdentity($values, array $column)
    {
        if (!empty($values[static::VARIANT_PREFIX . 'ID']) && !empty($values['sku'])) {
            $sku = trim(array_shift($values['sku']));

            foreach ($values[static::VARIANT_PREFIX . 'ID'] as $vId) {
                $entity = Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                    ->findOneBy(['variant_id' => $vId]);

                if ($entity && $entity->getProduct()->getSku() !== $sku) {
                    $this->addError('VARIANT-PRODUCT-FMT', ['column' => $column, 'value' => $vId]);
                }
            }
        }
    }

    /**
     * Verify 'attributes' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifyAttributes($value, array $column)
    {
        parent::verifyAttributes($value, $column);

        if (is_array($value)) {
            foreach ($value as $name => $attribute) {
                if ($this->isAttributeRowMultiline($attribute) && $this->isVariantValues($attribute)) {
                    foreach ($attribute as $offset => $line) {
                        foreach ($line as $val) {
                            if (empty($val)) {
                                $this->addError(
                                    'VARIANT-ATTRIBUTE-FMT',
                                    [
                                        'column' => array_merge($column, [static::COLUMN_NAME => $name]),
                                        'value'  => $attribute,
                                    ],
                                    $offset + 1 - $this->rowStartIndex
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Check if attribute column multiline(is variants)
     *
     * @param $attribute
     *
     * @return bool
     */
    protected function isAttributeRowMultiline($attribute)
    {
        $attribute = array_slice($attribute, 1);

        foreach ($attribute as $line) {
            foreach ($line as $value) {
                if (!empty($value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verify 'SKU' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifySku($value, array $column)
    {
        parent::verifySku($value, $column);

        if (!$this->verifyValueAsEmpty($value)) {
            $entity = Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                ->findOneBySku($value);

            if ($entity) {
                $this->addError('VARIANT-PRODUCT-SKU-FMT', ['column' => $column, 'value' => $value]);
            }
        }
    }

    /**
     * Verify 'variantSKU' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifyVariantSKU($value, array $column)
    {
        if (is_array($value)) {
            $processed = [];
            foreach ($value as $id => $sku) {
                $this->rowStartIndex = $id;
                if (!empty($sku)) {
                    if (array_search($sku, $processed) !== false) {
                        $this->addError('VARIANT-SKU-FMT', [
                            'column' => $column,
                            'value'  => $sku
                        ]);
                    } else if (Database::getRepo('XLite\Model\Product')->findOneBy(['sku' => $sku])) {
                        $this->addError('VARIANT-SKU-FMT', [
                            'column' => $column,
                            'value'  => $sku,
                        ]);
                    } elseif (
                        $variant = Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                            ->findOneBy(['sku' => $sku])
                    ) {
                        if (!in_array($variant, $this->variants, true)) {
                            $this->addError('VARIANT-SKU-FMT', [
                                'column' => $column,
                                'value' => $sku
                            ]);
                        }
                    }

                    $processed[] = $sku;
                }
            }
        }
    }

    /**
     * Verify 'variantPrice' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantPrice($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('VARIANT-PRICE-FMT', ['column' => $column, 'value' => $val]);
                }
            }
        }
    }

    /**
     * Verify 'variantQuantity' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifyVariantQuantity($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('VARIANT-QUANTITY-FMT', ['column' => $column, 'value' => $val]);
                }
            }
        }
    }

    /**
     * Verify 'variantWeight' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifyVariantWeight($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $val) {
                if (!$this->verifyValueAsFloat($val)) {
                    $this->addWarning('VARIANT-WEIGHT-FMT', ['column' => $column, 'value' => $val]);
                }
            }
        }
    }

    /**
     * Verify 'image' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifyVariantImage($value, array $column)
    {
        parent::verifyImages($value, $column);
    }

    // }}}

    // {{{ Import

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $this->prepareVariants($data);

        unset($data[static::VARIANT_PREFIX . 'ID']);

        return parent::importData($data);
    }

    /**
     * @param $data
     */
    protected function prepareVariants($data, $isVerification = false)
    {
        $this->variants = $this->variantsAttributes = [];

        $variantIdKey = static::VARIANT_PREFIX . 'ID';
        $this->variantIds = !empty($data[$variantIdKey]) ? $data[$variantIdKey] : [];
        if ($product = $this->detectModel($data)) {
            if (isset($data[$variantIdKey]) && is_array($data[$variantIdKey])) {
                foreach ($data[$variantIdKey] as $index => $vId) {
                    $entity = Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                        ->findOneBy(['variant_id' => $vId, 'product' => $product]);
                    if ($entity) {
                        $this->variants[$index] = $entity;
                    }
                }
            }

            if (isset($data['attributes'])) {
                $variantsAttributes = $this->getVariantsAttributes($product, $data['attributes']);

                if ($variantsAttributes) {
                    foreach ($variantsAttributes as $rowIndex => $values) {
                        if (!isset($this->variants[$rowIndex])) {
                            $values = $this->getAttributeValuesByData($product, $values);
                            $variant = $product->getVariantByAttributeValues($values, true);

                            if ($variant && !in_array($variant, $this->variants, true)) {
                                $this->variants[$rowIndex] = $variant;

                                if ($isVerification && !empty($data[$variantIdKey][$rowIndex])) {
                                    $this->addWarning('VARIANT-ID-MISMATCH', ['variantId' => $data[$variantIdKey][$rowIndex]], $rowIndex);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get variants attributes by attributes column data
     *
     * @param $model
     * @param $attributeValues
     * @return array
     */
    protected function getVariantsAttributes($model, $attributeValues)
    {
        $variantsAttributes = [];
        foreach ($attributeValues as $attr => $attributeValue) {
            if (!$this->isVariantValues($attributeValue)) {
                continue;
            }

            if ($attributeStringData = $this->parseAttributeString($attr)) {
                $type = $attributeStringData['type'];
                $name = $attributeStringData['name'];
                $productClass = 'class' === $type
                    ? $model->getProductClass()
                    : null;
                $product = 'product' === $type
                    ? $model
                    : null;
                $groupName = $attributeStringData['attributeGroup'] && 'product' !== $type
                    ? $this->getDefLangValue($attributeStringData['attributeGroup'])
                    : null;

                $values = [];
                foreach ($attributeValue as $value) {
                    $values = array_merge($values, $value);
                }
                $values = array_values(array_unique($values));
                $notEmptyValues = array_filter($values, function ($element) {
                    return $element !== "";
                });

                if (empty($notEmptyValues) || ('class' === $type && !$productClass)) {
                    continue;
                }

                $attributeGroup = null;

                if ($groupName) {
                    $attributeGroupCnd = new \XLite\Core\CommonCell();
                    $attributeGroupCnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_PRODUCT_CLASS} = $productClass;
                    $attributeGroupCnd->{\XLite\Model\Repo\AttributeGroup::SEARCH_NAME} = $groupName;
                    $result = \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->search($attributeGroupCnd);
                    if ($result) {
                        $attributeGroup = reset($result);
                    }
                }

                $attributeCnd = new \XLite\Core\CommonCell();

                if ($product && $product->getId()) {
                    $attributeCnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = $product;

                } else {
                    $attributeCnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;
                }

                $attributeCnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS}   = $productClass;
                $attributeCnd->{\XLite\Model\Repo\Attribute::SEARCH_ATTRIBUTE_GROUP} = $attributeGroup;
                $attributeCnd->{\XLite\Model\Repo\Attribute::SEARCH_NAME}            = $name;

                $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($attributeCnd);

                if ($attribute) {
                    $attribute = $attribute[0];

                } else {
                    $variantsAttributes = [];
                    break;
                }

                if ($attribute::TYPE_TEXT == $attribute->getType() && sizeof($attributeValue) > 1) {
                    $this->addError('VARIANT-TYPE-FMT', ['value' => $attr]);
                }

                foreach ($attributeValue as $k => $value) {
                    if (is_array($value)) {
                        $value = reset($value);
                    }
                    if ($valueStringData = $this->parseAttributeValueString($value)) {
                        $variantsAttributes[$k][$attribute->getId()][] = $valueStringData['value'];

                    } else {
                        $variantsAttributes[$k][$attribute->getId()][] = $value;
                    }
                }
            }
        }

        return $variantsAttributes;
    }

    /**
     * Get attribute values list by attribute values data [attributeId => attributeValueString]
     *
     * @param $model
     * @param $values
     * @return mixed
     */
    protected function getAttributeValuesByData($model, $values)
    {
        foreach ($values as $id => $value) {
            if (!isset($this->variantsAttributes[$id])) {
                $this->variantsAttributes[$id] = Database::getRepo('XLite\Model\Attribute')
                    ->find($id);
            }
            $attribute = $this->variantsAttributes[$id];

            $repo = Database::getRepo($attribute->getAttributeValueClass($attribute->getType()));
            if ($attribute::TYPE_CHECKBOX == $attribute->getType()) {
                if (is_array($value)) {
                    $value = reset($value);
                }
                $values[$id] = $repo->findOneBy(
                    [
                        'attribute' => $attribute,
                        'product'   => $model,
                        'value'     => $this->normalizeValueAsBoolean($value),
                    ]
                );
            } elseif ($attribute::TYPE_TEXT !== $attribute->getType()) {
                $attributeOption = Database::getRepo('XLite\Model\AttributeOption')
                    ->findOneByNameAndAttribute($value, $attribute);
                $values[$id] = $repo->findOneBy(
                    [
                        'attribute'        => $attribute,
                        'product'          => $model,
                        'attribute_option' => $attributeOption,
                    ]
                );
            }

        }

        return $values;
    }

    /**
     * Import 'attributes' value
     *
     * @param \XLite\Model\Product $model Product
     * @param array $value Value
     * @param array $column Column info
     */
    protected function importAttributesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        $this->variantsAttributes = [];

        foreach ($value as $k => $v) {
            if (!$this->isVariantValues($v)) {
                $value[$k] = array_splice($v, 0, 1);
            }
        }

        parent::importAttributesColumn($model, $value, $column);

        Database::getEM()->flush();

        if ($this->multAttributes) {
            $variantsAttributes = [];
            foreach ($this->multAttributes as $id => $values) {
                if ($this->isVariantValues($values)) {
                    foreach ($values as $k => $v) {
                        $variantsAttributes[$k][$id] = array_shift($v);
                    }
                } else {
                    unset($this->multAttributes[$id]);
                    continue;
                }
            }

            if ($variantsAttributes) {
                $variantsRepo = Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant');

                $tmp = [];
                foreach ($variantsAttributes as $k => $v) {
                    $tmp[$k] = implode('::', $v);
                }
                if (count($tmp) === count($variantsAttributes)) {
                    foreach ($variantsAttributes as $rowIndex => $values) {
                        $values = $this->getAttributeValuesByData($model, $values);
                        $variant = $model->getVariantByAttributeValues($values, true);

                        if (isset($this->variants[$rowIndex])) {
                            $idVariant = $this->variants[$rowIndex];
                        }

                        $oldVariantId = $variant
                            ? $variant->getVariantId()
                            : null;

                        if (!$variant || (isset($idVariant) && $idVariant->getId() !== $variant->getId())) {
                            if (isset($variant)) {
                                Database::getEM()->remove($variant);
                            }

                            if (isset($idVariant)) {
                                $variant = $idVariant;
                                $variant->getAttributeValueC()->clear();
                                $variant->getAttributeValueS()->clear();
                            } else {
                                $variant = $variantsRepo->insert(null, false);
                                $variant->setProduct($model);
                                $model->addVariants($variant);
                            }

                            foreach ($values as $attributeValue) {
                                $method = 'addAttributeValue' . $attributeValue->getAttribute()->getType();
                                $variant->$method($attributeValue);
                                $attributeValue->addVariants($variant);
                            }

                            if (!$oldVariantId) {
                                $variant->setVariantId($variantsRepo->assembleUniqueVariantId($variant));

                                if (isset($this->variantIds[$rowIndex]) && $this->variantIds[$rowIndex] !== $variant->getVariantId()) {
                                    $this->addWarning('VARIANT-ID-CHANGED', ['oldVariantId' => $this->variantIds[$rowIndex], 'newVariantId' => $variant->getVariantId()], $rowIndex);
                                }
                            }
                        }

                        $this->variants[$rowIndex] = $variant;
                    }
                }

                foreach ($model->getVariantsAttributes() as $va) {
                    $model->getVariantsAttributes()->removeElement($va);
                    $va->getVariantsProducts()->removeElement($model);
                }

                foreach ($this->variantsAttributes as $va) {
                    $model->addVariantsAttributes($va);
                    $va->addVariantsProducts($model);
                }

            }

            $model->assignDefaultVariant();
        } else {
            $model->checkVariants();
        }
    }

    /**
     * Check if values belong to variant(1 val for each row)
     *
     * @param array $values
     *
     * @return bool
     */
    protected function isVariantValues(array $values)
    {
        foreach ($values as $k => $value) {
            if (!is_array($value) || count($value) > 1 || !array_filter($value, function ($v) {
                    return $v !== '';
                })
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Import 'variantSKU' value
     *
     * @param \XLite\Model\Product $model Product
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function importVariantSKUColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setSku(isset($value[$rowIndex]) ? $value[$rowIndex] : '');
        }
    }

    /**
     * Import 'variantPrice' value
     *
     * @param \XLite\Model\Product $model Product
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function importVariantPriceColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setPrice($this->normalizeValueAsFloat(isset($value[$rowIndex]) ? $value[$rowIndex] : 0));
            $variant->setDefaultPrice(!isset($value[$rowIndex]));
        }
    }

    /**
     * Import 'variantQuantity' value
     *
     * @param \XLite\Model\Product $model Product
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function importVariantQuantityColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setAmount($this->normalizeValueAsUinteger(isset($value[$rowIndex]) ? $value[$rowIndex] : 0));
            $variant->setDefaultAmount(!isset($value[$rowIndex]));
        }
    }

    /**
     * Import 'variantdefaultValue' value
     *
     * @param \XLite\Model\Product $model Product
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function importVariantdefaultValueColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setDefaultValue($this->normalizeValueAsUinteger(isset($value[$rowIndex]) ? 1 : 0));
        }
    }

    /**
     * Import 'variantWeight' value
     *
     * @param \XLite\Model\Product $model Product
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function importVariantWeightColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $variant->setWeight($this->normalizeValueAsFloat(isset($value[$rowIndex]) ? $value[$rowIndex] : 0));
            $variant->setDefaultWeight(!isset($value[$rowIndex]));
        }
    }

    /**
     * Import 'variantImage' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @throws \Exception
     */
    protected function importVariantImageColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $oldImages = [];

        foreach ($this->variants as $rowIndex => $variant) {
            if (!isset($value[$rowIndex]) || $this->verifyValueAsNull($value[$rowIndex])) {
                $image = $variant->getImage();
                if ($image) {
                    Database::getEM()->remove($image);
                }
                $variant->setImage(null);

            } elseif (isset($value[$rowIndex]) && !$this->verifyValueAsEmpty($value[$rowIndex])) {
                $path = $value[$rowIndex];
                $file = $this->verifyValueAsLocalURL($path) ? $this->getLocalPathFromURL($path) : $path;
                if ($this->verifyValueAsFile($file)) {
                    /** @var \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image $oldImage */
                    $oldImage = $variant->getImage();
                    $image = new \XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image();

                    if ($this->verifyValueAsURL($file)) {
                        $success = $image->loadFromURL($file, true);

                    } else {
                        $success = $image->loadFromLocalFile(LC_DIR_ROOT . $file);
                    }

                    if (!$success) {
                        if ($image->getLoadError() === 'unwriteable') {
                            $this->addError('PRODUCT-IMG-LOAD-FAILED', [
                                'column' => $column,
                                'value'  => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file,
                            ]);
                        } elseif ($image->getLoadError()) {
                            $this->addError('PRODUCT-IMG-URL-LOAD-FAILED', [
                                'column' => $column,
                                'value'  => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file,
                            ]);
                        }
                    } else {
                        if ($oldImage) {
                            $oldImage->setProductVariant(null);
                            Database::getEM()->flush();
                            $oldImages[] = $oldImage;
                        }
                        $image->setProductVariant($variant);
                        $variant->setImage($image);
                        Database::getEM()->persist($image);
                    }

                } elseif (!$this->verifyValueAsFile($file) && $this->verifyValueAsURL($file)) {
                    $this->addWarning('PRODUCT-IMG-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $path,
                    ]);
                } else {
                    $this->addWarning('PRODUCT-IMG-NOT-VERIFIED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file,
                    ]);
                }
            }
        }

        foreach ($oldImages as $oldImage) {
            Database::getEM()->remove($oldImage);
        }
    }

    /**
     * Import 'image alt' value
     *
     * @param \XLite\Model\Product $model Product
     * @param array $value Value
     * @param array $column Column info
     */
    protected function importVariantImageAltColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            if (isset($value[$rowIndex])) {
                $alt = $value[$rowIndex];
                $image = $variant->getImage();
                if ($image) {
                    $image->setAlt($alt);
                }
            }
        }
    }

    // }}}
}
