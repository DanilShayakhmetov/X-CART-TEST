<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Admin;


use XLite\Core\Config;
use XLite\Core\Database;

 class Module extends \XLite\Controller\Admin\ModuleAbstract implements \XLite\Base\IDecorator
{
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        if ($this->getModuleId() === \Includes\Utils\Module\Module::buildId('XC', 'Reviews')) {
            $qb = Database::getRepo('XLite\Model\Notification')->createPureQueryBuilder('n');
            $qb->update()
                ->set(
                    'n.enabledForCustomer',
                    $qb->expr()->literal((boolean)Config::getInstance()->XC->Reviews->enableCustomersFollowup)
                )
                ->where($qb->expr()->eq('n.templatesDirectory', ':templatesDirectory'))
                ->setParameter('templatesDirectory', 'modules/XC/Reviews/review_key')
                ->getQuery()
                ->execute();
        }
    }
}