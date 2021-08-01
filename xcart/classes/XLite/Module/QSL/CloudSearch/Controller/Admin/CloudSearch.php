<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Controller\Admin;

use Includes\Utils\Module\Module;

/**
 * CloudSearch admin controller
 */
class CloudSearch extends \XLite\Controller\Admin\AAdmin
{
    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        $moduleId = Module::buildId('QSL', 'CloudSearch');

        $url = $this->buildURL('module', '', ['moduleId' => $moduleId]);

        $this->redirect($url);
    }
}