<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Model;

use XLite\Module\CDev\GoogleAnalytics;
use XLite\Module\CDev\GoogleAnalytics\Logic\Action;
use XLite\Module\CDev\GoogleAnalytics\Logic\BackendActionExecutor;

/**
 * Class Order
 */
abstract class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    public function addItem(\XLite\Model\OrderItem $newItem)
    {
        $result = parent::addItem($newItem);

        if ($result
            && !$this->addItemError
            && $this->shouldRegisterChange()
            && $newItem->getObject()
            && $newItem->getObject()->getCategory()
        ) {
            $category    = $newItem->getObject()->getCategory(
                \XLite\Core\Request::getInstance()->category_id
            );
            $translation = $category->getSoftTranslation(
                \XLite\Core\Config::getInstance()->General->default_language
            );

            if ($translation) {
                $newItem->setCategoryAdded(
                    $translation->getName()
                );
            }
        }

        return $result;
    }

    /**
     * Get order fingerprint for event subsystem
     *
     * @param array $exclude Exclude kes OPTIONAL
     * @param bool $init
     *
     * @return array
     */
    public function getEventFingerprint(array $exclude = [], $init = false)
    {
        $result = parent::getEventFingerprint($exclude, $init);

        if ($this->shouldRegisterChange()) {
            // Just for implementation without decoration of all excludeFingerprint implementations
            if (!isset($result['shippingMethodId']) && isset($result['shippingMethodName'])) {
                unset($result['shippingMethodName']);
            }

            if (!isset($result['paymentMethodId']) && isset($result['paymentMethodName'])) {
                unset($result['paymentMethodName']);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function defineFingerprintKeys()
    {
        $list = parent::defineFingerprintKeys();
        if ($this->shouldRegisterChange()) {
            return array_merge(
                $list,
                [
                    'shippingMethodName',
                    'paymentMethodName',
                ]
            );
        }

        return $list;
    }

    /**
     * Get fingerprint by 'shippingMethodName' key
     *
     * @return integer
     */
    protected function getFingerprintByShippingMethodName()
    {
        $shippingModifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        return $shippingModifier && $shippingModifier->getSelectedRate() && $shippingModifier->getSelectedRate()->getMethod()
            ? $shippingModifier->getSelectedRate()->getMethod()->getName()
            : '';
    }

    /**
     * Get fingerprint by 'paymentMethodName' key
     *
     * @return integer
     */
    protected function getFingerprintByPaymentMethodName()
    {
        return $this->getPaymentMethod()
            ? $this->getPaymentMethod()->getTitle()
            : '';
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processRegisterGAPurchase()
    {
        // If isPurchaseImmediatelyOnSuccess enabled 'purchase' was already registered, so skip STATUS_QUEUED
        if (GoogleAnalytics\Main::isPurchaseImmediatelyOnSuccess()
            && in_array($this->getOldPaymentStatusCode(), [
                \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
                \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED
            ], true)
        ) {
            return;
        }

        if ($this->shouldRegisterChange()) {
            BackendActionExecutor::execute(
                new Action\FullPurchaseAdmin($this)
            );
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processRegisterGARefund()
    {
        if ($this->shouldRegisterChange()) {
            BackendActionExecutor::execute(
                new Action\Refund($this)
            );
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processRegisterGARefundFromQueued()
    {
        // If we did not register purchase on checkout then we should not register refund
        if (!GoogleAnalytics\Main::isPurchaseImmediatelyOnSuccess()) {
            return;
        }

        if ($this->shouldRegisterChange()) {
            BackendActionExecutor::execute(
                new Action\Refund($this)
            );
        }
    }

    /**
     * @return bool
     */
    public function shouldRegisterChange()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && !$this->isTemporary();
    }
}