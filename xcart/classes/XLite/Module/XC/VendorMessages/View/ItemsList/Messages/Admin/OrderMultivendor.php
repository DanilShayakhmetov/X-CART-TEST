<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin;

/**
 * All admin messages
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin\Order implements \XLite\Base\IDecorator
{

    /**
     * @inheritdoc
     */
    protected function isEmailVisible(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        $result = parent::isEmailVisible($message);
        $auth = \XLite\Core\Auth::getInstance();
        $config = \XLite\Core\Config::getInstance()->XC->MultiVendor;

        return \XLite::isAdminZone()
            && (
                ($auth->isVendor() && $result && !$config->mask_contacts)
                || (!$auth->isVendor() && $result)
                || (!$auth->isVendor() && $message->getAuthor()->isVendor())
            );
    }

    /**
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message
     *
     * @return mixed
     */
    protected function getEmail(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        $email = parent::getEmail($message);

        return $message->getAuthor()->isVendor()
            ? $message->getAuthor()->getLogin()
            : $email;
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetParameters()
    {
        return parent::getWidgetParameters() + [
            'recipient_id' => \XLite\Core\Request::getInstance()->recipient_id,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getCommonParams()
    {
        $initialize = !isset($this->commonParams);

        $this->commonParams = parent::getCommonParams();

        if ($initialize) {
            $this->commonParams += [
                'recipient_id' => \XLite\Core\Request::getInstance()->recipient_id,
            ];
        }

        return $this->commonParams;
    }

}
