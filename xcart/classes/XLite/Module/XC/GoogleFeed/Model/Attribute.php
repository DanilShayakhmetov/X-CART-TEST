<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Model;

/**
 * Attribute
 *
 */
class Attribute extends \XLite\Model\Attribute implements \XLite\Base\IDecorator
{
    /**
     * Shopping group key
     *
     * @var string
     *
     * @Column (type="string", nullable=true)
     */
    protected $googleShoppingGroup;

    /**
     * @return array
     */
    public static function getGoogleShoppingGroups()
    {
        $defaultOptions = [
            'brand', 'color', 'pattern', 'material', 'size', 'size_type', 'size_system', 'age_group', 'gender', 'google_product_category'
        ];

        $configOptions = \XLite\Core\ConfigParser::getOptions(['google_product_feed', 'additional_options']) ?: [];

        return array_merge($defaultOptions, $configOptions);
    }

    /**
     * @return string
     */
    public function getGoogleShoppingGroup()
    {
        return $this->googleShoppingGroup;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setGoogleShoppingGroup($key)
    {
        if ($key === '' || in_array($key, static::getGoogleShoppingGroups(), true)) {
            $this->googleShoppingGroup = $key;
        }

        return $this;
    }
}
