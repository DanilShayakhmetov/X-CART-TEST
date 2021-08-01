<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Controller\Admin;


use Includes\Utils\Module\Manager;

 class Module extends \XLite\Module\XC\Reviews\Controller\Admin\Module implements \XLite\Base\IDecorator
{
    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        if ($this->getModule() === \Includes\Utils\Module\Module::buildId('XC', 'Geolocation')) {
            if (\XLite\Core\Request::getInstance()->restore_default_database) {
                if (\XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path
                    && file_exists(\XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path)
                ) {
                    unlink(\XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path);

                    \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                        'category' => 'XC\Geolocation',
                        'name'     => 'extended_db_path',
                        'value'    => '',
                    ]);
                }
            }
        }
    }
}