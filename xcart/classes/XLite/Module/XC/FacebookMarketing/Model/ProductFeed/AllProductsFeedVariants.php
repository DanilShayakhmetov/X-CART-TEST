<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Model\ProductFeed;

/**
 * AllProductsFeed
 *
 * @Decorator\Depend({"XC\GoogleFeed", "XC\ProductVariants"})
 */
class AllProductsFeedVariants extends \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed implements \XLite\Base\IDecorator
{
    const PARAM_VARIANT_GETTER_PREFIX = 'getVariantData';
    const VARIANT_GOOGLE_ATTRIBUTE_DATA_GETTER = 'getVariantGoogleAttributeData';

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        $headers = parent::getHeaders();

        $googleFields = $this->executeCachedRuntime(function () {
            $googleFields = [];
            if (0 < \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->count()) {
                $googleFields[] = [static::FIELD_PARAM_NAME => 'item_group_id'];
            }

            return $googleFields;
        }, ['google_fields_headers_variants']);

        return array_merge($headers, $googleFields);
    }

    /**
     * @inheritdoc
     *
     * @param \XLite\Model\AEntity $model
     */
    public function extractEntityData($model)
    {
        if ($model instanceof \XLite\Module\XC\ProductVariants\Model\ProductVariant) {
            \XLite\Core\Router::getInstance()->disableLanguageUrlsTmp();
            \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);
            $data = $this->extractVariantData($model);
            \XLite\Core\Router::getInstance()->releaseLanguageUrlsTmp();
            \XLite\Core\Translation::setTmpTranslationCode(null);

        } else {
            $data = parent::extractEntityData($model);
        }

        return $data;
    }

    /**
     * Extract data from product
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     *
     * @return array
     */
    public function extractVariantData($model)
    {
        $data = [];

        foreach ($this->getHeaders() as $field) {
            $fieldName = $field[static::FIELD_PARAM_NAME];
            if (
                isset($field[static::FIELD_PARAM_GETTER])
                && $field[static::FIELD_PARAM_GETTER] === static::GOOGLE_ATTRIBUTE_DATA_GETTER
            ) {
                $method = static::VARIANT_GOOGLE_ATTRIBUTE_DATA_GETTER;
            } else {
                $method = static::PARAM_VARIANT_GETTER_PREFIX . \Includes\Utils\Converter::convertToPascalCase($fieldName);
            }

            if (method_exists($this, $method)) {
                $data[] = $this->{$method}($model, $fieldName);

            } else {
                $method = isset($field[static::FIELD_PARAM_GETTER])
                    ? $field[static::FIELD_PARAM_GETTER]
                    : static::PARAM_PRODUCT_GETTER_PREFIX . \Includes\Utils\Converter::convertToPascalCase($fieldName);

                if (method_exists($this, $method)) {
                    $data[] = $this->{$method}($model->getProduct(), $fieldName);
                } else {
                    $data[] = null;
                }
            }
        }

        return $data;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return bool
     */
    protected function checkIfDuplicate(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        foreach ($model->getValues() as $attrValue) {
            /** @var \XLite\Model\Attribute $attr */
            $attr = $attrValue->getAttribute();

            $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
            $availableFields = \XLite\Model\Attribute::getGoogleShoppingGroups();
            if (!$attributeGoogleGroup || !in_array($attributeGoogleGroup, $availableFields, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract data from product variant
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     *
     * @return array
     */
    protected function collectVariantGoogleFeedAttributes($model)
    {
        return $this->executeCachedRuntime(function () use ($model) {
            $result = [];
            foreach ($model->getValues() as $attrValue) {
                /** @var \XLite\Model\Attribute $attr */
                $attr = $attrValue->getAttribute();

                $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
                $availableFields = \XLite\Model\Attribute::getGoogleShoppingGroups();
                if ($attributeGoogleGroup && in_array($attributeGoogleGroup, $availableFields, true)) {
                    $result[$attributeGoogleGroup] = $attrValue->asString();
                }
            }

            return $result;

        }, ['collectVariantGoogleFeedAttributes', $model->getId()]);
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataId($entity, $fieldName)
    {
        return $entity->getSku() ?: $entity->getVariantId();
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     *
     * Possible values: in stock, out of stock, preorder, available for order, discontinued
     */
    protected function getVariantDataAvailability($entity, $fieldName)
    {
        if (!$entity->availableInDate()) {
            return 'preorder';
        }

        return $entity->isOutOfStock()
            ? 'out of stock'
            : 'in stock';
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataInventory($entity, $fieldName)
    {
        if ($entity->getDefaultAmount()) {
            $result = $this->getEntityDataInventory($entity->getProduct(), $fieldName);

        } else {
            $result = $entity->getAmount();
        }

        return $result;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataImageLink($entity, $fieldName)
    {
        if ($entity->getImage()) {
            return $entity->getImage()->getGoogleFeedURL();
        }

        return $this->getEntityDataImageLink($entity->getProduct(), $fieldName);
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataLink($entity, $fieldName)
    {
        $values = array_reduce($entity->getValues(), function ($obj, $value) {
            $obj[$value->getAttribute()->getId()] = $value->getId();
            return $obj;
        }, []);

        return $entity->getProduct()->getProductId()
            ? \XLite\Core\Converter::buildFullURL(
                'product',
                '',
                [
                    'product_id'       => $entity->getProduct()->getProductId(),
                    'attribute_values' => $values
                ],
                \XLite::CART_SELF,
                null,
                true
            )
            : '';
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataTitle($entity, $fieldName)
    {
        $attrsString = array_reduce($entity->getValues(), function ($str, $attr) {
            $str .= $attr->asString() . ' ';
            return $str;
        }, '');

        return $entity->getProduct()->getName() . ' ' . trim($attrsString);
    }

    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataPrice($entity, $fieldName)
    {
        return $this->getEntityDataPrice($entity, $fieldName);
    }

    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataSalePrice($entity, $fieldName)
    {
        return $this->getEntityDataSalePrice($entity, $fieldName);
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataMpn($entity, $fieldName)
    {
        return $this->isNeedDefaultVariantMpn($entity) ? $this->getDefaultVariantMpn($entity) : '';
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataCondition($entity, $fieldName)
    {
        $feedAttributes = array_merge(
            $this->collectGoogleFeedAttributes($entity->getProduct()),
            $this->collectVariantGoogleFeedAttributes($entity)
        );

        $result = isset($feedAttributes['condition'])
            ? $feedAttributes['condition']
            : $this->getEntityDataCondition($entity->getProduct(), $fieldName);

        return $result;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantGoogleAttributeData($entity, $fieldName)
    {
        $feedAttributes = array_merge(
            $this->collectGoogleFeedAttributes($entity->getProduct()),
            $this->collectVariantGoogleFeedAttributes($entity)
        );

        $method = static::GOOGLE_ATTRIBUTE_DATA_GETTER;

        $result = isset($feedAttributes[$fieldName])
            ? $feedAttributes[$fieldName]
            : $this->{$method}($entity->getProduct(), $fieldName);

        return $result;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     *
     * @return string
     */
    protected function getVariantDataItemGroupId($entity, $fieldName)
    {
        $result = '';

        if (!$this->checkIfDuplicate($entity)) {
            $result = $entity->getProduct()->getSku();
        }

        return $result;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @return bool
     */
    protected function isNeedDefaultVariantMpn($entity)
    {
        foreach ($this->getHeaders() as $header) {
            if (in_array($header[static::FIELD_PARAM_NAME], ['brand', 'gtin'])) {
                $fieldValue = '';

                $fieldName = $header[static::FIELD_PARAM_NAME];
                if (
                    isset($header[static::FIELD_PARAM_GETTER])
                    && $header[static::FIELD_PARAM_GETTER] === static::GOOGLE_ATTRIBUTE_DATA_GETTER
                ) {
                    $method = static::VARIANT_GOOGLE_ATTRIBUTE_DATA_GETTER;
                } else {
                    $method = static::PARAM_VARIANT_GETTER_PREFIX . \Includes\Utils\Converter::convertToPascalCase($fieldName);
                }

                if (method_exists($this, $method)) {
                    $fieldValue = $this->{$method}($entity, $fieldName);

                } else {
                    $method = isset($header[static::FIELD_PARAM_GETTER])
                        ? $header[static::FIELD_PARAM_GETTER]
                        : static::PARAM_PRODUCT_GETTER_PREFIX . \Includes\Utils\Converter::convertToPascalCase($fieldName);

                    if (method_exists($this, $method)) {
                        $fieldValue = $this->{$method}($entity->getProduct(), $fieldName);
                    }
                }

                if ('' !== $fieldValue) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @return string
     */
    protected function getDefaultVariantMpn($entity)
    {
        return $entity->getVariantId();
    }
}