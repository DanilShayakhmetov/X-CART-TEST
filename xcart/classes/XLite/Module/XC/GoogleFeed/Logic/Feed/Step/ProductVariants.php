<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Logic\Feed\Step;

use XLite\Module\XC\GoogleFeed\Main;
use XLite\Module\XC\GoogleFeed\Model\Attribute;
use XLite\Core\Converter;

/**
 * Products step
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariants extends \XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products implements \XLite\Base\IDecorator
{
    // {{{ Row processing

    /**
     * Process item
     *
     * @param \XLite\Module\XC\ProductVariants\Model\Product $model
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $canProcessVariants = Main::shouldExportDuplicates() || !$this->hasDuplicateVariants($model);
        if ($model->hasVariants() && $canProcessVariants) {
            \XLite\Core\Router::getInstance()->disableLanguageUrlsTmp();
            \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);
            foreach ($model->getVariants() as $variant) {
                $this->generator->addToRecord($this->getVariantRecord($variant));
            }
            \XLite\Core\Router::getInstance()->releaseLanguageUrlsTmp();
            \XLite\Core\Translation::setTmpTranslationCode(null);
        } else {
            parent::processModel($model);
        }
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantTitle(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        $attrsString = array_reduce($model->getValues(), function ($str, $attr) {
            $str .= $attr->asString() . ' ';
            return $str;
        }, '');

        return $model->getProduct()->getName() . ' ' . trim($attrsString);
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantId(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $model->getSku() ?: $model->getVariantId();
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantAvailability(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        if (!$model->availableInDate()) {
            return 'preorder';
        }

        return $model->isOutOfStock()
            ? 'out of stock'
            : 'in stock';
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantLink(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        $values = array_reduce($model->getValues(), function ($obj, $value) {
            $obj[$value->getAttribute()->getId()] = $value->getId();
            return $obj;
        }, []);

        return $model->getProduct()->getProductId()
            ? Main::getShopURL(
                Converter::buildURL(
                    'product',
                    '',
                    [
                        'product_id'       => $model->getProduct()->getProductId(),
                        'attribute_values' => $values
                    ],
                    \XLite::getCustomerScript(),
                    true
                )
            )
            : '';
    }

    /**
     * @param \XLite\Model\Product $model
     * @param int $offset
     * @return array
     */
    protected function getVariantAdditionalImages(\XLite\Model\Product $model, $offset = 0)
    {
        $result = [];

        foreach ($model->getPublicImages() as $image) {
            if ($image) {
                $result[] = mb_substr($image->getGoogleFeedURL(), 0, self::LINK_LENGTH);
            }
        }

        if ($result) {
            $result = array_slice($result, $offset, 9 + $offset);
        }

        return implode("</g:additional_image_link><g:additional_image_link>", $result);
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantPrice(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        $currency = \XLite::getInstance()->getCurrency();
        $parts = $currency->formatParts($model->getDisplayPrice());
        unset($parts['prefix'], $parts['suffix'], $parts['sign']);
        $parts['code'] = ' ' . strtoupper($currency->getCode());

        return implode('', $parts);
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantMpn(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $this->getMpn($model->getProduct());
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantGtin(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $this->getGtin($model->getProduct());
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return array
     */
    protected function getVariantRecord(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        $result = [
            'g:id'                => mb_substr($this->getVariantId($model), 0, self::SKU_LENGTH),
            'g:link'              => mb_substr($this->getVariantLink($model), 0, self::LINK_LENGTH),
            'g:title'             => mb_substr($this->getVariantTitle($model), 0, self::TITLE_LENGTH),
            'g:description'       => mb_substr(trim(strip_tags($model->getProduct()->getProcessedDescription())), 0, self::DESCRIPTION_LENGTH),
            'g:price'             => $this->getVariantPrice($model),
            'g:availability'      => $this->getVariantAvailability($model),
            'g:condition'         => $this->getCondition($model->getProduct()),
            'g:gtin'              => $this->getVariantGtin($model),
            'g:mpn'               => $this->getVariantMpn($model),
            'g:product_type'      => $this->getProductType($model->getProduct()),
            'g:shipping_weight'   => $this->getWeight($model)
        ];

        foreach ($model->getValues() as $attrValue) {
            /** @var \XLite\Module\XC\GoogleFeed\Model\Attribute $attr */
            $attr = $attrValue->getAttribute();

            $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
            if ($attributeGoogleGroup && in_array($attributeGoogleGroup, Attribute::getGoogleShoppingGroups(), true)) {
                $result['g:' . $attributeGoogleGroup] = $attrValue->asString();
            }
        }

        foreach ($model->getProduct()->getGoogleFeedParams() as $attrName => $data) {
            $attr = $data['attr'];
            $value = $data['value'];

            $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
            if ($attributeGoogleGroup && in_array($attributeGoogleGroup, Attribute::getGoogleShoppingGroups(), true)) {
                $result['g:' . $attributeGoogleGroup] = is_object($value) ? $value->asString() : (string)$value;
            }
        }

        if (!$this->checkIfDuplicate($model)) {
            $result['g:item_group_id'] = mb_substr($this->getRecordId($model->getProduct()), 0, self::SKU_LENGTH);
        }

        if ($model->getImage()) {
            $result['g:image_link'] = mb_substr($model->getImage()->getGoogleFeedURL(), 0, self::LINK_LENGTH);
        }

        if ($model->getProduct()->countImages() > 0) {
            if (isset($result['g:image_link'])) {
                $offset = 0;
            } else {
                $result['g:image_link'] = $model->getProduct()->getImage()->getGoogleFeedURL();
                $offset = 1;
            }

            $result['g:additional_image_link'] = $this->getVariantAdditionalImages($model->getProduct(), $offset);
        }

        if (!$model->availableInDate()) {
            $availabilityDate = date('Y-m-d', $model->getProduct()->getArrivalDate()) . 'T' . date('H:i:s', $model->getProduct()->getArrivalDate()) . 'Z';
            $result['g:availability_date'] = $availabilityDate;
        }

        // Doesn't require shipping
        if ($model->getProduct()->getFreeShipping()) {
            $result['g:shipping'] = $this->getShippingRecord($model->getProduct());
        }

        if (empty($result['g:brand']) || (empty($result['g:gtin']) && empty($result['g:mpn']))) {
            $result['g:identifier_exists'] = 'false';
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Product $model
     * @return bool
     */
    protected function hasDuplicateVariants(\XLite\Model\Product $model)
    {
        if ($model->hasVariants()) {
            foreach ($model->getVariants() as $variant) {
                if ($this->checkIfDuplicate($variant)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return bool
     */
    protected function checkIfDuplicate(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        foreach ($model->getValues() as $attrValue) {
            /** @var \XLite\Module\XC\GoogleFeed\Model\Attribute $attr */
            $attr = $attrValue->getAttribute();

            if (!in_array($attr->getGoogleShoppingGroup(), Attribute::getGoogleShoppingGroups(), true)) {
                return true;
            }
        }

        return false;
    }
}