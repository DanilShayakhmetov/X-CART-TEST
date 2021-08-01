<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Cart;

use XLite\Model\Cart;
use XLite\Module\XC\MailChimp\Core\Request\Campaign as MailChimpCampaign;
use XLite\Module\XC\MailChimp\Core\Request\Customer as MailChimpCustomer;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpBatchRequest;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Core\Request\Product as MailChimpProduct;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;
use XLite\Module\XC\MailChimp\Logic\DataMapper;

class UpdateOrCreate extends MailChimpRequest
{
    protected $customerId;

    protected $userIdFromRequest;

    protected $productsData = [];

    /**
     * @param Cart $cart
     */
    public function __construct($cart)
    {
        $campaignIdFromRequest = MailChimpCampaign\Get::getCampaignIdFromRequest();
        $userIdFromRequest     = MailChimpCustomer\Get::getUserIdFromRequest();
        $trackingIdFromRequest = MailChimpRequest::getTrackingIdFromRequest();

        $this->customerId        = $cart->getProfile()->getProfileId();
        $this->userIdFromRequest = $userIdFromRequest;

        $cartData = DataMapper\Cart::getDataByCart($campaignIdFromRequest, $trackingIdFromRequest, $cart);

        parent::__construct('', '', '', $cartData);

        $this->productsData = array_map(static function ($item) {
            return DataMapper\Product::getDataByOrderItem($item);
        }, $cart->getItems()->toArray());
    }

    /**
     * @param Cart $cart
     *
     * @return self
     */
    public static function getRequest($cart): self
    {
        return new self($cart);
    }

    /**
     * @param Cart $cart
     *
     * @return mixed
     */
    public static function scheduleAction($cart)
    {
        return self::getRequest($cart)->schedule();
    }

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        $cartData = $this->getArgs();

        $completeCustomerData = null;

        foreach (MailChimpStore\Get::getActiveStores($cartData['campaign_id'] ?? null) as $storeId => $storeData) {
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

            $cartData['customer'] = $customerData;

            if (!Check::executeAction($storeId, $cartData['id'])) {
                MailChimpStore\Create::executeAction([
                    'id'            => $storeId,
                    'name'          => $storeData['storeName'],
                    'list_id'       => $storeData['listId'],
                    'currency_code' => $cartData['currency_code'],
                    'money_format'  => \XLite::getInstance()->getCurrency()->getPrefix() ?: \XLite::getInstance()->getCurrency()->getSuffix(),
                ]);

                Check::dropActionCache($storeId, $cartData['id']);
                MailChimpCustomer\Check::dropActionCache($storeId, $cartData['customer']['id']);

                $this->setName('Creating cart');
                $this->setMethod('post');
                $this->setAction("ecommerce/stores/{$storeId}/carts");

            } else {
                $this->setName('Updating cart');
                $this->setMethod('patch');
                $this->setAction("ecommerce/stores/{$storeId}/carts/{$cartData['id']}");
            }

            $this->setArgs($cartData);

            $batchRequest->addOperation($this);

            $batchRequest->execute();
        }

        return null;
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
