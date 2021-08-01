<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\StickyPanel\FormModel;

class NonStickyPanel extends \XLite\View\StickyPanel\FormModel\Panel
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/Onboarding/wizard_steps/nonsticky_panel.js';

        return $list;
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        return str_replace('sticky-panel', 'non-sticky-panel', $class);
    }
}
