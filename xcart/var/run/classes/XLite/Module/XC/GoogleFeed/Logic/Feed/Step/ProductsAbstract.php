<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Logic\Feed\Step;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Module\XC\GoogleFeed\Model\Attribute;

/**
 * Products step
 */
abstract class ProductsAbstract extends AFeedStep
{
    use ExecuteCachedTrait;

    const SKU_LENGTH         = 50;
    const TITLE_LENGTH       = 150;
    const LINK_LENGTH        = 2000;
    const DESCRIPTION_LENGTH = 5000;

    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('XLite\Model\Product');
    }

    // }}}

    // {{{ Row processing

    /**
     * Process item
     *
     * @param \XLite\Model\Product $model
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        \XLite\Core\Router::getInstance()->disableLanguageUrlsTmp();
        \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);
        $this->generator->addToRecord($this->getProductRecord($model));
        \XLite\Core\Router::getInstance()->releaseLanguageUrlsTmp();
        \XLite\Core\Translation::setTmpTranslationCode(null);
    }

    // }}}

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getRecordId(\XLite\Model\Product $model)
    {
        return $model->getSku();
    }

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getAvailability(\XLite\Model\Product $model)
    {
        if (!$model->availableInDate()) {
            return 'preorder';
        }

        return $model->isOutOfStock()
            ? 'out of stock'
            : 'in stock';
    }

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getLink(\XLite\Model\Product $model)
    {
        return $model->getProductId()
            ? \XLite::getInstance()->getShopURL(
                Converter::buildURL(
                    'product',
                    '',
                    ['product_id' => $model->getProductId()],
                    \XLite::getCustomerScript(),
                    true
                )
            )
            : '';
    }

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getPrice(\XLite\Model\Product $model)
    {
        $currency = \XLite::getInstance()->getCurrency();
        $parts = $currency->formatParts($model->getDisplayPrice());
        unset($parts['prefix'], $parts['suffix'], $parts['sign']);
        $parts['code'] = ' ' . strtoupper($currency->getCode());

        return implode('', $parts);
    }

    /**
     * @param \XLite\Model\Product $model
     * @param float $value
     * @return string
     */
    protected function getShippingRecord(\XLite\Model\Product $model, $value = 0.0)
    {
        $currency = \XLite::getInstance()->getCurrency();
        $parts = $currency->formatParts($value);
        unset($parts['prefix'], $parts['suffix'], $parts['sign']);
        $parts['code'] = ' ' . strtoupper($currency->getCode());

        $price = implode('', $parts);

        return ['g:price' => $price];
    }

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getCondition(\XLite\Model\Product $model)
    {
        return 'new';
    }

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getMpn(\XLite\Model\Product $model)
    {
        return '';
    }

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getGtin(\XLite\Model\Product $model)
    {
        return '';
    }

    /**
     * @param \XLite\Model\Product $model
     * @return array
     */
    protected function getAdditionalImages(\XLite\Model\Product $model)
    {
        $result = [];

        foreach ($model->getPublicImages() as $image) {
            if ($image) {
                $result[] = $image->getGoogleFeedURL();
            }
        }

        if ($result) {
            $result = array_slice($result, 1, 10);
        }

        return implode("</g:additional_image_link><g:additional_image_link>", $result);
    }

    /**
     * @param \XLite\Model\Product $model
     * @return string
     */
    protected function getProductType(\XLite\Model\Product $model)
    {
        $result = '';

        if ($category = $model->getCategory()) {
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
     * @param \XLite\Model\AEntity $model
     * @return string
     */
    protected function getWeight(\XLite\Model\AEntity $model)
    {
        $unit = Config::getInstance()->Units->weight_unit;
        $weight = $model->getClearWeight();

        if ($unit !== 'kg' && $unit !== 'lbs') {
            $unit = $unit === 'g' ? 'kg' : 'lbs';
            $weight = Converter::convertWeightUnits(
                $weight,
                Config::getInstance()->Units->weight_unit,
                $unit
            );
        }

        return $weight . ' ' . $unit;
    }

    /**
     * @param \XLite\Model\AEntity $model
     * @return string
     */
    protected function getBoxLength(\XLite\Model\AEntity $model)
    {
        $unit = Config::getInstance()->Units->dim_unit;
        $value = $model->getBoxLength();

        if ($unit !== 'in' && $unit !== 'cm') {
            $unit = in_array($unit, ['mm', 'dm', 'm']) ? 'cm' : 'in';
            $value = Converter::convertDimensionUnits(
                $value,
                Config::getInstance()->Units->dim_unit,
                $unit
            );
        }

        return $value > 0 ? $value . ' ' . $unit : false;
    }

    /**
     * @param \XLite\Model\AEntity $model
     * @return string
     */
    protected function getBoxWidth(\XLite\Model\AEntity $model)
    {
        $unit = Config::getInstance()->Units->dim_unit;
        $value = $model->getBoxWidth();

        if ($unit !== 'in' && $unit !== 'cm') {
            $unit = in_array($unit, ['mm', 'dm', 'm']) ? 'cm' : 'in';
            $value = Converter::convertDimensionUnits(
                $value,
                Config::getInstance()->Units->dim_unit,
                $unit
            );
        }

        return $value > 0 ? $value . ' ' . $unit : false;
    }

    /**
     * @param \XLite\Model\AEntity $model
     * @return string
     */
    protected function getBoxHeight(\XLite\Model\AEntity $model)
    {
        $unit = Config::getInstance()->Units->dim_unit;
        $value = $model->getBoxHeight();

        if ($unit !== 'in' && $unit !== 'cm') {
            $unit = in_array($unit, ['mm', 'dm', 'm']) ? 'cm' : 'in';
            $value = Converter::convertDimensionUnits(
                $value,
                Config::getInstance()->Units->dim_unit,
                $unit
            );
        }

        return $value > 0 ? $value . ' ' . $unit : false;
    }

    /**
     * @param \XLite\Model\Product $model
     * @return array
     */
    protected function getProductRecord(\XLite\Model\Product $model)
    {
        $result = [
            'g:id'                => mb_substr($this->getRecordId($model), 0, self::SKU_LENGTH),
            'g:link'              => mb_substr($this->getLink($model), 0, self::LINK_LENGTH),
            'g:title'             => mb_substr($model->getName(), 0, self::TITLE_LENGTH),
            'g:description'       => mb_substr(trim(strip_tags($model->getProcessedDescription())), 0, self::DESCRIPTION_LENGTH),
            'g:price'             => $this->getPrice($model),
            'g:availability'      => $this->getAvailability($model),
            'g:condition'         => $this->getCondition($model),
            'g:gtin'              => $this->getGtin($model),
            'g:mpn'               => $this->getMpn($model),
            'g:product_type'      => $this->getProductType($model),
            'g:shipping_weight'   => $this->getWeight($model),
        ];

        $boxLength = $this->getBoxLength($model);
        $boxWidth = $this->getBoxWidth($model);
        $boxHeight = $this->getBoxHeight($model);
        if (
            $boxLength
            && $boxWidth
            && $boxHeight
        ) {
            $result['g:shipping_length'] = $boxLength;
            $result['g:shipping_width'] = $boxWidth;
            $result['g:shipping_height'] = $boxHeight;
        }

        foreach ($model->getGoogleFeedParams() as $attrName => $data) {
            /** @var \XLite\Module\XC\GoogleFeed\Model\Attribute $attr */
            $attr = $data['attr'];
            $value = $data['value'];

            $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
            if ($attributeGoogleGroup && in_array($attributeGoogleGroup, Attribute::getGoogleShoppingGroups(), true)) {
                $result['g:' . $attributeGoogleGroup] = is_object($value) ? $value->asString() : (string)$value;
            }
        }

        if ($model->getImage()) {
            $result['g:image_link'] = mb_substr($model->getImage()->getGoogleFeedURL(), 0, self::LINK_LENGTH);
        }

        if ($model->countImages() > 1) {
            $result['g:additional_image_link'] = $this->getAdditionalImages($model);
        }

        if (!$model->availableInDate()) {
            $availabilityDate = date('Y-m-d', $model->getArrivalDate()) . 'T' . date('H:i:s', $model->getArrivalDate()) . 'Z';
            $result['g:availability_date'] = $availabilityDate;
        }

        // Doesn't require shipping
        if ($model->getFreeShipping()) {
            $result['g:shipping'] = $this->getShippingRecord($model);
        }

        if (empty($result['g:brand']) || (empty($result['g:gtin']) && empty($result['g:mpn']))) {
            $result['g:identifier_exists'] = 'false';
        }

        return $result;
    }

}