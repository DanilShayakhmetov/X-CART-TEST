<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Model\ProductFeed;

use XLite\View\AView;

/**
 * AllProductsFeed
 */
abstract class AllProductsFeedAbstract implements \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\IProductFeed
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    const FIELD_PARAM_NAME            = 'name';
    const FIELD_PARAM_GETTER          = 'getter';
    const PARAM_PRODUCT_GETTER_PREFIX = 'getEntityData';

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return [
            [static::FIELD_PARAM_NAME => 'id'],
            [static::FIELD_PARAM_NAME => 'availability'],
            [static::FIELD_PARAM_NAME => 'inventory'],
            [static::FIELD_PARAM_NAME => 'product_type'],
            [static::FIELD_PARAM_NAME => 'condition'],
            [static::FIELD_PARAM_NAME => 'description'],
            [static::FIELD_PARAM_NAME => 'image_link'],
            [static::FIELD_PARAM_NAME => 'link'],
            [static::FIELD_PARAM_NAME => 'title'],
            [static::FIELD_PARAM_NAME => 'price'],
            [static::FIELD_PARAM_NAME => 'sale_price'],
            [static::FIELD_PARAM_NAME => 'mpn'],
            [static::FIELD_PARAM_NAME => 'additional_image_link'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getHeaderRow()
    {
        return array_map(function ($element) {
            return $element[static::FIELD_PARAM_NAME];
        }, $this->getHeaders());
    }

    /**
     * @inheritdoc
     *
     * @param \XLite\Model\AEntity $model
     */
    public function extractEntityData($model)
    {
        $data = [];

        if ($model instanceof \XLite\Model\Product) {
            \XLite\Core\Router::getInstance()->disableLanguageUrlsTmp();
            \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);
            $data = $this->extractProductData($model);
            \XLite\Core\Router::getInstance()->releaseLanguageUrlsTmp();
            \XLite\Core\Translation::setTmpTranslationCode(null);
        }

        return $data;
    }

    /**
     * Extract data from product
     *
     * @param \XLite\Model\Product $model
     *
     * @return array
     */
    public function extractProductData($model)
    {
        $data = [];

        foreach ($this->getHeaders() as $field) {
            $fieldName = $field[static::FIELD_PARAM_NAME];
            $method = isset($field[static::FIELD_PARAM_GETTER])
                ? $field[static::FIELD_PARAM_GETTER]
                : static::PARAM_PRODUCT_GETTER_PREFIX . \Includes\Utils\Converter::convertToPascalCase($fieldName);

            if (method_exists($this, $method)) {
                $data[] = $this->{$method}($model, $fieldName);
            } else {
                $data[] = null;
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getServiceName()
    {
        return 'main_feed';
    }

    /**
     * Return file path
     *
     * @return string
     */
    public function getStoragePath()
    {
        return \XLite\Module\XC\FacebookMarketing\Core\ProductFeedDataWriter::getDataDir()
               . \Includes\Utils\FileManager::sanitizeFilename($this->getServiceName());
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataId($entity, $fieldName)
    {
        return $entity->getSku();
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     *
     * Possible values: in stock, out of stock, preorder, available for order, discontinued
     */
    protected function getEntityDataAvailability($entity, $fieldName)
    {
        if (!$entity->availableInDate()) {
            return 'preorder';
        }

        return $entity->isOutOfStock()
            ? 'out of stock'
            : 'in stock';
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataInventory($entity, $fieldName)
    {
        return $entity->getInventoryEnabled() ? $entity->getAmount() : '1000';
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataProductType($entity, $fieldName)
    {
        $result = '';

        if ($category = $entity->getCategory()) {
            $path = $category->getPath();
            $path = array_map(function ($v) {
                return $v->getName();
            }, $path);

            while (strlen(implode(' > ', $path)) > 750 && !empty($path)) {
                array_shift($path);
            }

            $result = implode(' > ', $path);
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     *
     * Possible values: new, refurbished, used
     */
    protected function getEntityDataCondition($entity, $fieldName)
    {
        return 'new';
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataDescription($entity, $fieldName)
    {
        return strip_tags($entity->getCommonDescription());
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataImageLink($entity, $fieldName)
    {
        return $entity->getImageURL();
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataLink($entity, $fieldName)
    {
        return \XLite\Core\Converter::buildFullURL(
            'product',
            '',
            ['product_id' => $entity->getId()],
            \XLite::CART_SELF,
            null,
            true
        );
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataTitle($entity, $fieldName)
    {
        return $entity->getName();
    }

    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataPrice($entity, $fieldName)
    {
        return $this->formatPrice($entity->getDisplayPrice()) . ' ' . \XLite::getInstance()->getCurrency()->getCode();
    }

    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataSalePrice($entity, $fieldName)
    {
        return '';
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataMpn($entity, $fieldName)
    {
        return $this->isNeedDefaultMpn($entity) ? $this->getDefaultMpn($entity) : '';
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     *
     * Possible values: new, refurbished, used
     */
    protected function getEntityDataAdditionalImageLink($entity, $fieldName)
    {
        $result = [];

        foreach ($entity->getPublicImages() as $image) {
            if ($image) {
                $result[] = $image->getURL();
            }
        }

        if ($result) {
            $result = array_slice($result, 1, 10);
        }

        return implode(',', $result);
    }

    /**
     * @param \XLite\Model\Product $entity
     * @return bool
     */
    protected function isNeedDefaultMpn($entity)
    {
        foreach ($this->getHeaders() as $header) {
            if (in_array($header[static::FIELD_PARAM_NAME], ['brand', 'gtin'])) {
                $method = isset($header[static::FIELD_PARAM_GETTER])
                    ? $header[static::FIELD_PARAM_GETTER]
                    : static::PARAM_PRODUCT_GETTER_PREFIX . \Includes\Utils\Converter::convertToPascalCase($header[static::FIELD_PARAM_NAME]);
                if (method_exists($this, $method) && '' != $this->{$method}($entity, $header[static::FIELD_PARAM_NAME])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param \XLite\Model\Product $entity
     * @return string
     */
    protected function getDefaultMpn($entity)
    {
        return $entity->getId();
    }

    /**
     * @param      $value
     * @param null $currency
     *
     * @return string
     */
    protected function formatPrice($value, $currency = null)
    {
        if (null === $currency) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        $parts = $currency->formatParts($value);

        if (isset($parts['sign']) && '-' === $parts['sign']) {
            $parts['sign'] = '− ';
        }

        unset($parts['suffix']);
        unset($parts['prefix']);

        return implode('', $parts);
    }
}