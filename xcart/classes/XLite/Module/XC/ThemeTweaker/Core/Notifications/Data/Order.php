<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;

class Order extends Provider
{
    use ExecuteCachedTrait;

    public function getData($templateDir)
    {
        return $this->getOrder($templateDir);
    }

    public function getName($templateDir)
    {
        return 'order';
    }

    public function getSuitabilityErrors($templateDir)
    {
        $errors = parent::getSuitabilityErrors($templateDir);

        $order = $this->getOrder($templateDir);

        if (
            $templateDir === 'order_tracking_information'
            && $order
            && $order->getTrackingNumbers()->isEmpty()
        ) {
            $errors[] = [
                'code'  => 'no_tracking',
                'value' => $order->getOrderNumber(),
            ];
        }

        return $errors;
    }

    public function validate($templateDir, $value)
    {
        if (!$this->findOrderByNumber($value)) {
            return [
                [
                    'code'  => 'order_nf',
                    'value' => $value,
                ],
            ];
        }

        return [];
    }

    public function isAvailable($templateDir)
    {
        return !!$this->getData($templateDir);
    }

    protected function getTemplateDirectories()
    {
        return [
            'order_canceled',
            'order_changed',
            'order_created',
            'order_failed',
            'order_processed',
            'order_shipped',
            'order_tracking_information',
            'failed_transaction',
            'order_waiting_for_approve',
            'backorder_created',
        ];
    }

    /**
     * @param string $templateDir
     *
     * @return \XLite\Model\Order|null
     */
    protected function getOrder($templateDir)
    {
        return $this->executeCachedRuntime(function () use ($templateDir) {
            $order = $this->findOrderByNumber($this->getValue($templateDir));
            return ($order && $order instanceOf \XLite\Model\Order)
                ? $order : Database::getRepo('XLite\Model\Order')->findDumpOrder();
        }, $templateDir);
    }

    /**
     * @param $number
     *
     * @return \XLite\Model\AEntity|null
     */
    protected function findOrderByNumber($number)
    {
        return $number
            ? Database::getRepo('XLite\Model\Order')->findOneByOrderNumber($number)
            : null;
    }
}
