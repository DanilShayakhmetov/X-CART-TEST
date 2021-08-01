<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel for import completed page.
 */
class ImportCompleted extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        $list = [];
        $list['new_import'] = $this->getWidget(
            [
                'label'    => static::t('New import'),
                'style' => 'main-button',
                'location' => $this->buildURL($this->getImportTarget())
            ],
            'XLite\View\Button\Link'
        );

        return $list;
    }

    /**
     * Check - sticky panel is active only if form is changed
     *
     * @return boolean
     */
    protected function isFormChangeActivation()
    {
        return false;
    }
}
