<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp;

use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Logger;

 class XLite extends \XLite\Module\XC\ThemeTweaker\XLite implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    public function processRequest()
    {
        $result = parent::processRequest();
        
        try {
            // See BUG-5263
            if (!file_exists(LC_DIR_CLASSES . str_replace('\\', LC_DS, '\XLite\Module\XC\MailChimp\Core\MailChimpQueue') . '.php')){
                return $result;
            }

            $actions = MailChimpQueue::getInstance()->getActions();

            foreach ($actions as $action) {
                $action->execute();
            }

            MailChimpQueue::getInstance()->clearActions();
        } catch (\Exception $e) {
            Logger::getInstance()->log('Error while executing MailChimpQueue actions. '. $e->getMessage());
        }
        
        return $result;
    }

}
