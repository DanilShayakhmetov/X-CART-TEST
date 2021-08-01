<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Controller\Admin;

use Includes\Utils\Module\Manager;

/**
 * Module settings
 */
class Module extends \XLite\Controller\Admin\Module implements \XLite\Base\IDecorator
{
    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        $request = \XLite\Core\Request::getInstance();
        if (
            $this->getModuleID()
            && $this->getModule() === \Includes\Utils\Module\Module::buildId('CDev', 'PINCodes')
            && $request->action === 'update'
        ) {

            if (isset($request->esd_fullfilment)) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                    'category' => 'CDev\Egoods',
                    'name'     => 'esd_fullfilment',
                    'value'    => (boolean)$request->esd_fullfilment,
                ]);
            }

            if (isset($request->approve_before_download)) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                    'category' => 'CDev\Egoods',
                    'name'     => 'approve_before_download',
                    'value'    => (boolean)$request->approve_before_download,
                ]);
            }
        }

        parent::handleRequest();
    }
}