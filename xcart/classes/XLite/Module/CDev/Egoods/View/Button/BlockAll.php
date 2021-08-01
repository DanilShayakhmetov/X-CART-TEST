<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Button;


/**
 * BlockAll
 */
class BlockAll extends \XLite\View\Button\Link
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $targets = parent::getAllowedTargets();
        $targets[] = 'order';

        return $targets;
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder()
            && $this->hasActiveEgoods()
            && \XLite\Core\Request::getInstance()->page === 'egoods';
    }

    protected function getDefaultLabel()
    {
        return 'Block all';
    }

    protected function getDefaultStyle()
    {
        return 'button egoods block-all';
    }

    /**
     * Defines the default location path
     *
     * @return null|string
     */
    protected function getDefaultLocation()
    {
        return $this->buildURL('order', 'egoods_block_all', [
            'order_number'                            => $this->getOrder()->getOrderNumber(),
            \XLite\Controller\AController::RETURN_URL => \Includes\Utils\URLManager::getCurrentURL(),
        ]);
    }

    /**
     * @return \XLite\Model\Order|null
     */
    protected function getOrder()
    {
        return \XLite::getController()->getOrder();
    }

    protected function hasActiveEgoods()
    {
        foreach ($this->getOrder()->getPrivateAttachments() as $attachment) {
            /* @var \XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $attachment */
            if ($attachment->isAvailable()) {
                return true;
            }
        }

        return false;
    }
}