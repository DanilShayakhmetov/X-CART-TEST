<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

use Doctrine\ORM\Event\LifecycleEventArgs;
use XLite\Module\XC\MailChimp\Core\Action;
use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Module\XC\MailChimp\Core\Request\Product as MailChimpProduct;
use XLite\Module\XC\MailChimp\Logic\DataMapper;
use XLite\Module\XC\MailChimp\Main;

abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Use product in segment conditions
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $useAsSegmentCondition = false;

    /**
     * @param LifecycleEventArgs $event
     *
     * @PostPersist
     */
    public function prepareBeforeCreateCorrect(LifecycleEventArgs $event)
    {
        if (!$this->getProductId()) {
            $this->product_id = $event->getEntity()->getProductId();
        }

        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpProduct\Create::scheduleAction($store->getId(), DataMapper\Product::getDataByProduct($this));
            }
        }
    }

    /**
     * @PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        parent::prepareBeforeUpdate();

        $changeSet = \XLite\Core\Database::getEM()->getUnitOfWork()->getEntityChangeSet($this);

        foreach ($this->getExcludedFieldsForMailchimpUpdate() as $field) {
            if (isset($changeSet[$field])) {
                unset($changeSet[$field]);
            }
        }

        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()
            && $this->getProductId()
            && array_filter($changeSet)
        ) {
            MailChimpQueue::getInstance()->addAction(
                'productUpdate' . $this->getProductId(),
                new Action\ProductUpdate($this)
            );
        }
    }

    /**
     * @PreRemove
     */
    public function prepareBeforeRemove()
    {
        parent::prepareBeforeRemove();

        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpProduct\Remove::scheduleAction($store->getId(), $this->getProductId());
            }
        }
    }

    /**
     * @return string
     */
    public function getFrontURLForMailChimp(): string
    {
        return $this->getProductId()
            ? \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL(
                    'product',
                    '',
                    $this->getParamsForFrontURL(),
                    \XLite::getCustomerScript()
                )
            )
            : '';
    }

    /**
     * Get useAsSegmentCondition
     *
     * @return boolean
     */
    public function getUseAsSegmentCondition()
    {
        return $this->useAsSegmentCondition;
    }

    /**
     * Set useAsSegmentCondition
     *
     * @param boolean $useAsSegmentCondition
     *
     * @return Product
     */
    public function setUseAsSegmentCondition($useAsSegmentCondition)
    {
        $this->useAsSegmentCondition = $useAsSegmentCondition;

        return $this;
    }

    /**
     * Return list of excluded fields
     *
     * @return array
     */
    protected function getExcludedFieldsForMailchimpUpdate(): array
    {
        return ['enabled'];
    }
}
