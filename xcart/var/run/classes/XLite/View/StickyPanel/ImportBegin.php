<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel for import begin page.
 */
class ImportBegin extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        $list = [];
        $list['begin_import'] = $this->getWidget(
            [
                'label'    => static::t('Start Import'),
                'style' => 'main-button regular-main-button submit disabled',
                'jsCode' => 'this.form.submit();'
            ],
            'XLite\View\Button\ProgressState'
        );

        return $list;
    }
}
