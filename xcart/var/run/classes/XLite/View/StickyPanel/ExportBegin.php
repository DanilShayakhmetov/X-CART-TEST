<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel for export begin page.
 */
class ExportBegin extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        $list = [];

        $disabled = $this->isExportLocked();
        $label = $this->isExportLocked()
            ? static::t('Please wait')
            : static::t('Start Export');

        $list['begin_export'] = $this->getWidget(
            [
                'label'    => $label,
                'style'    => 'main-button regular-main-button submit',
                'disabled' => $disabled,
                'jsCode'   => 'this.form.submit();'
            ],
            '\XLite\View\Button\ProgressState'
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

    /**
     * Get export state
     *
     * @return boolean
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }
}
