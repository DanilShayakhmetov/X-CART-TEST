<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\FormField\Textarea;

/**
 * FroalaAdvanced
 *
 * @Decorator\Depend("XC\FroalaEditor")
 */
class FroalaDescription extends \XLite\Module\XC\CustomProductTabs\View\FormField\Textarea\Description implements \XLite\Base\IDecorator
{
    /**
     * Returns current theme style files
     *
     * @return array
     */
    protected function getThemeStyles()
    {
        $files = parent::getThemeStyles();

        $path = \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'modules/XC/CustomProductTabs/froala/description.css',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite::CUSTOMER_INTERFACE
        );

        if ($path) {
            $files[] = $this->getShopURL($path, null, ['t' => LC_START_TIME]);
        }

        return $files;
    }
}