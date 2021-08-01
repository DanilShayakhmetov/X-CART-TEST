<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\StickyPanel;

/**
 * Panel for Egoods page.
 */
class Egoods extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        return [
            'block_all' => $this->getWidget(
                [
                    'label'    => static::t('Block all'),
                    'style'    => 'regular-button',
                ],
                'XLite\Module\CDev\Egoods\View\Button\BlockAll'
            ),
            'renew_all' => $this->getWidget(
                [
                    'label'    => static::t('Renew all'),
                    'style'    => 'regular-button',
                ],
                'XLite\Module\CDev\Egoods\View\Button\RenewAll'
            )
        ];
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
