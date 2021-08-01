<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel for last exported page.
 */
class ExportLast extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        return [
            'new_export'         => $this->getWidget(
                [
                    'label'    => static::t('New Export'),
                    'style'    => 'main-button regular-main-button',
                    'location' => static::buildURL('export', '', ['page' => 'new'])
                ],
                '\XLite\View\Button\Link'
            ),
            'download_all_files' => $this->getWidget(
                [
                    'label'         => static::t('Download all files'),
                    'dropDirection' => 'dropup',
                    'style'         => 'more-action last-visible'
                ],
                'XLite\View\Button\Dropdown\DownloadAllFiles'
            ),
            'delete_all_files'         => $this->getWidget(
                [
                    'label'    => static::t('Delete all files'),
                    'style'    => 'regular-button',
                    'location' => static::buildURL('export', 'deleteFiles')
                ],
                '\XLite\View\Button\Link'
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
