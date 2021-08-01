<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Controller\Admin;

/**
 * Order controller
 */
abstract class Order extends \XLite\Module\CDev\GoogleAnalytics\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    // {{{ Actions

    /**
     * Block egood link
     */
    protected function doActionEgoodsBlock()
    {
        $id = \XLite\Core\Request::GetInstance()->attachment_id;
        $attachment = \XLite\Core\Database::getRepo('XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment')
            ->find($id);
        if ($attachment) {
            $attachment->setBlocked(true);
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\TopMessage::addInfo('Download link is blocked');

        } else {
            \XLite\Core\TopMessage::addError('Download link did not found');
        }
    }

    /**
     * Block egood link
     */
    protected function doActionEgoodsBlockAll()
    {
        foreach ($this->getOrder()->getPrivateAttachments() as $attachment) {
            $attachment->setBlocked(true);
            $changed = true;
        }

        if (isset($changed)) {
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\TopMessage::addInfo('Download links is blocked');
        }
    }

    /**
     * Renew egood link
     */
    protected function doActionEgoodsRenew()
    {
        $id = \XLite\Core\Request::GetInstance()->attachment_id;
        $attachment = \XLite\Core\Database::getRepo('XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment')
            ->find($id);
        if (!$attachment) {
            \XLite\Core\TopMessage::addError('Download link did not found');

        } elseif (!$attachment->isActive()) {
            \XLite\Core\TopMessage::addError('Download link is not active');

        } else {
            $attachment->renew();
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Mailer::sendEgoodsLinks($attachment->getItem()->getOrder());
            \XLite\Core\TopMessage::addInfo('Download link is renew');
        }
    }

    /**
     * Renew egood link
     */
    protected function doActionEgoodsRenewAll()
    {
        foreach ($this->getOrder()->getPrivateAttachments() as $attachment) {
            $attachment->renew();
            $changed = true;
        }

        if (isset($changed)) {
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\Mailer::sendEgoodsLinks($attachment->getItem()->getOrder());
            \XLite\Core\TopMessage::addInfo('Download links is renew');
        }
    }

    // }}}

    // {{{ Tabs

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        $order = $this->getOrder();
        if ($order && $order->getPrivateAttachments()) {
            $list['egoods'] = static::t('E-goods');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        $list['egoods'] = 'modules/CDev/Egoods/order.twig';

        return $list;
    }

    // }}}
}
