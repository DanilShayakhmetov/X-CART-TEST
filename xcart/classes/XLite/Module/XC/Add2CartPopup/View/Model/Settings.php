<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View\Model;

use Includes\Utils\Module\Manager;

/**
 * General settings widget extention
 */
class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/XC/Add2CartPopup/style.css';

        return $list;
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ('redirect_to_cart' === $option->getName() && $cell) {
            $url = Manager::getRegistry()->getModuleServiceURL('XC', 'Add2CartPopup');

            $cell[static::SCHEMA_COMMENT] = static::t(
                'This option is ignored as Add to Cart Popup module is installed and enabled.',
                ['url' => $url]
            );
            $cell[static::SCHEMA_CLASS] = 'XLite\View\FormField\Input\Hidden';
        }

        return $cell;
    }
}
