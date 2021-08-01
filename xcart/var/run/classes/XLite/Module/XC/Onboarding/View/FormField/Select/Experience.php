<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormField\Select;

class Experience extends \XLite\View\FormField\Select\Regular
{

    protected function getDefaultOptions()
    {
        return [
            ''                     => static::t('Select'),
            'not_selling'          => static::t('I\'m not selling yet'),
            'another_solution'     => static::t('I sell online using another solution'),
            'online'               => static::t('I sell through online marketplaces'),
            'offline'              => static::t('I sell offline'),
            'customer'             => static::t('I’m an X-Cart customer'),
            'building_for_another' => static::t('I\'m building a store for another person'),
        ];
    }
}