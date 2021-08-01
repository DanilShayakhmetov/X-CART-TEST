<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Password with button
 */
class PasswordWithButton extends \XLite\View\FormField\Input\Password implements \XLite\Core\PreloadedLabels\ProviderInterface
{

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input_with_button.twig';
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/input_with_button/controller.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'form_field/input_with_button/style.less'
            ]
        );
    }
}
