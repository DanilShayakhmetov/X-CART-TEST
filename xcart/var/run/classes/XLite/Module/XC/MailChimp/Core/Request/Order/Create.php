<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Order;

use XLite\Model\Order;
use XLite\Module\XC\MailChimp\Core\Request\Campaign as MailChimpCampaign;
use XLite\Module\XC\MailChimp\Core\Request\Customer as MailChimpCustomer;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpBatchRequest;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Core\Request\Product as MailChimpProduct;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;
use XLite\Module\XC\MailChimp\Logic\DataMapper;

class Create extends MailChimpRequest
{
    protected $customerId;

    protected $userIdFromRequest;

    protected $orderId;

    protected $productsData = [];

    /**
     * @param Order $order
     */
    public function __construct($order)
    {
        $campaignIdFromRequest = MailChimpCampaign\Get::getCampaignIdFromRequest();
        $userIdFromRequest     = MailChimpCustomer\Get::getUserIdFromRequest();
        $trackingIdFromRequest = MailChimpRequest::getTrackingIdFromRequest();

        $this->customerId        = $order->getOrigProfile() && $order->getOrigProfile()->getProfileId()
            ? $order->getOrigProfile()->getProfileId()
            : null;
        $this->userIdFromRequest = $userIdFromRequest;

        $orderData = DataMapper\Order::getDataByOrder($campaignIdFromRequest, $trackingIdFromRequest, $order);

        parent::__construct('Creating order', 'post', '', $orderData);

        $this->orderId      = $order->getOrderId();
        $this->productsData = array_map(static function ($item) {
            return DataMapper\Product::getDataByOrderItem($item);
        }, $order->getItems()->toArray());
    }

    /**
     * @param Order $order
     *
     * @return self
     */
    public static function getRequest($order): self
    {
        return new self($order);
    }

    /**
     * @param Order $order
     *
     * @return mixed
     */
    public static function scheduleAction($order)
    {
        return self::getRequest($order)->schedule();
    }

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        $orderData = $this->getArgs();

        $completeCustomerData = null;

        foreach (MailChimpStore\Get::getActiveStores($orderData['campaign_id'] ?? null) as $storeId => $storeData) {
            $batchRequest = MailChimpBatchRequest::getRequest(array_map(static function ($item) use ($storeId) {
                return MailChimpProduct\Create::getRequest($storeId, $item);
            }, $this->productsData));

            $customerExists = MailChimpCustomer\Check::executeAction($storeId, $this->customerId ?: $this->userIdFromRequest);

            if ($customerExists) {
                $customerData = ['id' => $this->customerId ?: $this->userIdFromRequest];
            } else {
                if ($completeCustomerData === null) {
                    $profile              = $this->getProfileByCustomerId($this->customerId);
                    $completeCustomerData = DataMapper\Customer::getDataForOrder($this->userIdFromRequest, $profile, false);
                }

                $customerData = $completeCustomerData;
            }

            $orderData['customer'] = $customerData;

            MailChimpStore\Create::executeAction([
                'id'            => $storeId,
                'name'          => $storeData['storeName'],
                'list_id'       => $storeData['listId'],
                'currency_code' => $orderData['currency_code'],
                'money_format'  => \XLite::getInstance()->getCurrency()->getPrefix() ?: \XLite::getInstance()->getCurrency()->getSuffix(),
            ]);

            $this->setStoreIdToOrder($this->orderId, $storeId);

            $this->setAction("ecommerce/stores/{$storeId}/orders");
            $this->setArgs($orderData);

            $batchRequest->addOperation($this);

            MailChimpCustomer\Check::dropActionCache($storeId, $orderData['customer']['id']);

            $batchRequest->execute();
        }

        return null;
    }

    /**
     * @param string $orderId
     * @param string $storeId
     */
    protected function setStoreIdToOrder($orderId, $storeId): void
    {
        /** @var \XLite\Module\XC\MailChimp\Model\Order|Order $order */
        $order = \XLite\Core\Database::getRepo(Order::class)->find($orderId);

        if ($order) {
            $order->setMailchimpStoreId($storeId);
        }
    }

    /**
     * @param string $customerId
     *
     * @return \XLite\Model\Profile|null
     */
    protected function getProfileByCustomerId($customerId)
    {
        /** @var \XLite\Model\Profile $profile */
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($customerId);

        return $profile;
    }
}
