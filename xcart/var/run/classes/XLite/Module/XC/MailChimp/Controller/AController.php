<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller;

use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Logger;

 class AController extends \XLite\Module\XC\ThemeTweaker\Controller\AController implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    protected function run()
    {
        parent::run();

        try {
            // See BUG-5263
            if (!file_exists(LC_DIR_CLASSES . str_replace('\\', LC_DS, '\XLite\Module\XC\MailChimp\Core\MailChimpQueue') . '.php')){
                return;
            }
            $actions = MailChimpQueue::getInstance()->getActions();

            foreach ($actions as $action) {
                $action->execute();
            }

            MailChimpQueue::getInstance()->clearActions();
        } catch (\Exception $e) {
            Logger::getInstance()->log('Error while executing MailChimpQueue actions. '. $e->getMessage());
        }
    }


}
