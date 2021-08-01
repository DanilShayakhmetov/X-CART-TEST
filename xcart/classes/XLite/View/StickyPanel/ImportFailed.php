<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel for import failed page.
 */
class ImportFailed extends \XLite\View\Base\FormStickyPanel
{
    const PARAM_IS_DISPLAY_PROCEED_BUTTON = 'isDisplayProceedButton';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_IS_DISPLAY_PROCEED_BUTTON => new \XLite\Model\WidgetParam\TypeBool('Is display proceed button')
        );
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        $list = [];

        if ($this->getParam(self::PARAM_IS_DISPLAY_PROCEED_BUTTON)) {
            $list['continue_import'] = $this->getWidget(
                [
                    'label'    => static::t('Proceed import'),
                    'style'    => 'main-button regular-main-button',
                    'location' => $this->buildURL($this->getImportTarget(), 'proceed')
                ],
                'XLite\View\Button\Link'
            );
        } else {
            $list['continue_import'] = $this->getWidget(
                [
                    'label'    => static::t('Proceed import'),
                    'style'    => 'main-button regular-main-button',
                    'disabled' => true,
                    'jsCode' => 'return false'
                ],
                'XLite\View\Button\Link'
            );
        }

        $list['reupload'] = $this->getWidget(
            [
                'label'    => static::t('Reupload files'),
                'style' => 'main-button',
                'location' => $this->buildURL($this->getImportTarget(), 'reset')
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
