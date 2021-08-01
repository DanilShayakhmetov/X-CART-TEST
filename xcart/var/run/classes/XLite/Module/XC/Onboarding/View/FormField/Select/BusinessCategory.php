<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormField\Select;

class BusinessCategory extends \XLite\View\FormField\Select\Regular
{
    protected function getDefaultOptions()
    {
        return [
            ''                    => static::t('Select category'),
            'automotive'          => static::t('Automotive'),
            'clothing_fashion'    => static::t('Clothing & fashion'),
            'jewelry_accessories' => static::t('Jewelry & accessories'),
            'electronics'         => static::t('Electronics'),
            'food_drink'          => static::t('Food & drink'),
            'furniture'           => static::t('Furniture'),
            'health_beauty'       => static::t('Health & Beauty'),
            'home_garden'         => static::t('Home & Garden'),
            'sports_equipment'    => static::t('Sports Equipment'),
            'pet_products'        => static::t('Pet Products'),
            'toys_games'          => static::t('Toys & Games'),
            'arts_hobbies'        => static::t('Arts & Hobbies'),
            'other'               => static::t('Other'),
        ];
    }
}