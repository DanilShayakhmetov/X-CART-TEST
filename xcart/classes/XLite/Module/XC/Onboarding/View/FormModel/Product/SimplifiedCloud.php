<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormModel\Product;

use XLite\View\Button\AButton;
use XLite\View\Button\Link;
use XLite\View\Button\SimpleLink;
use XLite\View\Button\Submit;
use XLite\View\FormField\AFormField;

class SimplifiedCloud extends Simplified
{
    protected function getFormButtons()
    {
        return [
            'submit'    => new Submit([
                AButton::PARAM_LABEL         => 'Proceed to the next step',
                AButton::PARAM_BTN_TYPE      => 'regular-main-button',
                AButton::PARAM_STYLE         => 'action',
                AFormField::PARAM_ATTRIBUTES => [
                    'data-dirty'    => static::t('Proceed to the next step'),
                    'data-pristine' => static::t('Proceed to the next step'),
                ],
            ]),
            'dashboard' => new SimpleLink([
                Link::PARAM_LABEL      => 'Go to the dashboard',
                Link::PARAM_STYLE      => 'always-enabled external muted-button',
                Link::PARAM_ATTRIBUTES => [
                    '@click' => 'hideWizard',
                ],
                Link::PARAM_JS_CODE    => 'null',
            ]),
        ];
    }
}