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
 * @Decorator\Depend("XC\GoogleFeed")
 */
class AllProductsFeedGoogle extends \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed implements \XLite\Base\IDecorator
{
    const GOOGLE_ATTRIBUTE_DATA_GETTER = 'getEntityGoogleAttributeData';

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        $headers = parent::getHeaders();

        $googleFields = $this->executeCachedRuntime(function () {
            $usedGroups = \XLite\Core\Database::getRepo('\XLite\Model\Attribute')->getUsedGoogleGroupNames();
            $availableFields = array_intersect(\XLite\Model\Attribute::getGoogleShoppingGroups(), $usedGroups);

            $googleFields = [];
            foreach ($availableFields as $field) {
                $method = static::PARAM_PRODUCT_GETTER_PREFIX . \Includes\Utils\Converter::convertToPascalCase($field);
                $fieldData = [static::FIELD_PARAM_NAME => $field];

                if (!method_exists($this, $method)) {
                    $method = static::GOOGLE_ATTRIBUTE_DATA_GETTER;
                }

                $fieldData[static::FIELD_PARAM_GETTER] = $method;
                $googleFields[] = $fieldData;
            }

            return $googleFields;
        }, ['google_fields_headers']);

        return array_merge($headers, $googleFields);
    }

    /**
     * @param \XLite\Model\Product $model
     * @return array
     */
    protected function collectGoogleFeedAttributes(\XLite\Model\Product $model)
    {
        return $this->executeCachedRuntime(function () use ($model) {
            $result = [];

            foreach ($model->getGoogleFeedParams() as $attrName => $data) {
                /** @var \XLite\Model\Attribute $attr */
                $attr = $data['attr'];
                $value = $data['value'];

                $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
                $availableFields = \XLite\Model\Attribute::getGoogleShoppingGroups();
                if ($attributeGoogleGroup && in_array($attributeGoogleGroup, $availableFields, true)) {
                    $result[$attributeGoogleGroup] = is_object($value) ? $value->asString() : (string)$value;
                }
            }

            return $result;
        }, ['collectGoogleFeedAttributes', $model->getProductId()]);
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataCondition($entity, $fieldName)
    {
        $feedAttributes = $this->collectGoogleFeedAttributes($entity);

        $result = isset($feedAttributes['condition'])
            ? $feedAttributes['condition']
            : parent::getEntityDataCondition($entity, $fieldName);

        return $result;
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityGoogleAttributeData($entity, $fieldName)
    {
        $feedAttributes = $this->collectGoogleFeedAttributes($entity);

        $result = isset($feedAttributes[$fieldName])
            ? $feedAttributes[$fieldName]
            : '';

        return $result;
    }
}