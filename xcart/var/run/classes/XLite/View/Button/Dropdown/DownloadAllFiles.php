<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Download all files
 */
class DownloadAllFiles extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = [];

        foreach ($this->getAllowedPackTypes() as $type) {
            $list[$type] = [
                'class'  => 'XLite\View\Button\Link',
                'params' => [
                    'label'      => $type,
                    'icon-style' => 'icon-zip',
                    'location'   => static::buildURL('export', 'pack', ['type' => $type])
                ],
                'position' => 100,
            ];
        }

        return $list;
    }

    /**
     * Get allowed pack types
     *
     * @return array
     */
    protected function getAllowedPackTypes()
    {
        return $this->getGenerator()->getAllowedArchives();
    }
}
