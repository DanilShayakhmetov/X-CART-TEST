<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * ConsistencyCheck sticky panel
 */
class ConsistencyCheck extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' consistency-check-panel');

        return $class;
    }

    /**
     * Buttons list (cache)
     *
     * @var array
     */
    protected $buttonsList;

    /**
     * Get buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        if (!isset($this->buttonsList)) {
            $this->buttonsList = $this->defineButtons();
        }

        return $this->buttonsList;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = array();
        $list['refresh'] = $this->getSaveWidget();

        return $list;
    }

    /**
     * Get "save" widget
     *
     * @return \XLite\View\AView
     */
    protected function getSaveWidget()
    {
        $location = $this->buildURL(
            'consistency_check',
            'start'
        );

        return $this->getWidget(
            array(
                'style'    => 'btn regular-main-button refresh',
                'label'    => static::t('Refresh consistency status'),
                'jsCode'   => 'self.location = \'' . $location . '\';',
                'disabled' => false,
            ),
            'XLite\View\Button\ProgressState'
        );
    }
}
