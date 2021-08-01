<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Model;


/**
 * The "product" model class
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product is available for Google feed
     *
     * @var boolean
     *
     * @Column (type="boolean", options={"default" : true})
     */
    protected $googleFeedEnabled = true;

    /**
     * @return bool
     */
    public function getGoogleFeedEnabled()
    {
        return $this->googleFeedEnabled;
    }

    /**
     * @param bool $googleFeedEnabled
     * @return \XLite\Model\Product
     */
    public function setGoogleFeedEnabled($googleFeedEnabled)
    {
        $this->googleFeedEnabled = $googleFeedEnabled;
        return $this;
    }


    /**
     * @return array
     */
    public function getGoogleFeedParams()
    {
        $result = [];
        $attrs = $this->defineGoogleFeedAttributes();

        /** @var \XLite\Model\Attribute $attr */
        foreach ($attrs as $attr) {
            $key = $attr->getName();
            $value = $attr->getAttributeValue($this, true);
            $result[$key] = [
                'attr'  => $attr,
                'value' => is_array($value) ? reset($value) : $value,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function defineGoogleFeedAttributes()
    {
        return $this->executeCachedRuntime(function () {
            $result = [];

            foreach ((array)\XLite\Model\Attribute::getTypes() as $type => $name) {
                $class = \XLite\Model\Attribute::getAttributeValueClass($type);
                if (
                    is_subclass_of($class, 'XLite\Model\AttributeValue\Multiple')
                    || '\XLite\Model\AttributeValue\AttributeValueHidden' === $class
                ) {
                    $result[] = \XLite\Core\Database::getRepo($class)->findNonMultipleAttributesGoogleFeed($this);

                } elseif ('\XLite\Model\AttributeValue\AttributeValueText' === $class) {
                    $result[] = \XLite\Core\Database::getRepo($class)->findNonEditableAttributesGoogleFeed($this);
                }
            }

            $result = (array)call_user_func_array('array_merge', $result);

            if ($result) {
                foreach ($result as $k => $v) {
                    $result[$k] = $v[0];
                }
            }

            return $result;
        }, ['googleFeedAttributes']);
    }
}