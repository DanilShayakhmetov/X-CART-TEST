<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Module\CDev\GoSocial\Model;

/**
 * Product
 *
 * @Decorator\Depend("CDev\GoSocial")
 */
 class Product extends \XLite\Module\CDev\GoSocial\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineAdditionalMetaTags()
    {
        return parent::defineAdditionalMetatags() + $this->getOgProductCategory();
    }

    /**
     * @return array
     */
    protected function getOgProductCategory()
    {
        return $this->executeCachedRuntime(function () {
            $result = [];

            if ($this->getProductId()) {
                $attrs = $this->defineGoogleFeedAttributes();

                foreach ($attrs as $attr) {
                    if ($attr->getGoogleShoppingGroup() == 'google_product_category') {
                        $attrValue = $attr->getAttributeValue($this, true);
                        if (is_array($attrValue)) {
                            $attrValue = reset($attrValue);
                        }

                        $result['product:category'] = is_object($attrValue)
                            ? $attrValue->asString()
                            : (string) $attrValue;

                        break;
                    }
                }
            }

            return $result;
        }, ['google_product_category', $this->getProductId()]);
    }
}
