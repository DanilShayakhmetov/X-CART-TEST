<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Console;

class PageNotFound extends \XLite\Controller\Console\AConsole
{
    protected function doNoAction()
    {
        parent::doNoAction();

        $this->printError('Target not found');
    }
}
