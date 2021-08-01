<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Controller\Admin;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\Track;

/**
 * Export controller
 */
class Export extends \XLite\Controller\Admin\Export implements \XLite\Base\IDecorator
{
    /**
     * Export action
     *
     * @return void
     */
    protected function doActionExport()
    {
        $request = \XLite\Core\Request::getInstance();

        if (in_array('XLite\Logic\Export\Step\Products', $request->section)) {
            $props = [
                'export-as'        => $request->options['files'] ?? '',
                'export-data'      => $request->options['attrs'] ?? '',
                'export-charset'   => $request->options['charset'] ?? '',
                'export-delimiter' => $request->options['delimiter'] ?? '',
            ];

            Mediator::getInstance()->addMessage(
                new Track('Export products', $props)
            );
        }

        parent::doActionExport();
    }
}
