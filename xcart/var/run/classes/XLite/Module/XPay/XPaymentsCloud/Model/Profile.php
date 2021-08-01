<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model;

use \XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * X-Payments Specific fields
 *
 */
 class Profile extends \XLite\Model\ProfileAbstract implements \XLite\Base\IDecorator
{
    /**
     * X-Payments Customer Id
     * 
     * @var string
     *
     * @Column (type="string")
     */
    protected $xpaymentsCustomerId = '';

    /**
     * Cache of X-Payments cards and related data
     *
     * @var \stdClass
     */
    protected $xpaymentsCardsCache = null;

    /**
     * Returns X-Payments Customer Id
     * 
     * @return string
     */
    public function getXpaymentsCustomerId()
    {
        return $this->xpaymentsCustomerId;
    }

    /**
     * Set X-Payments Customer Id
     *
     * @param $xpaymentsCustomerId
     *
     * @return Profile
     */
    public function setXpaymentsCustomerId($xpaymentsCustomerId)
    {
        $this->xpaymentsCustomerId = $xpaymentsCustomerId;

        return $this;
    }

    /**
     * Get X-Payments Saved cards limit
     *
     * @return \stdClass
     */
    public function getXpaymentsTokenizationSettings()
    {
        $this->validateXpaymentsCardsCache();
        return $this->xpaymentsCardsCache;
    }

    /**
     * Get X-Payments Saved cards
     *
     * @return array
     */
    public function getXpaymentsCards()
    {
        $this->validateXpaymentsCardsCache();
        return $this->xpaymentsCardsCache['cards'];
    }

    /**
     * Makes API call to get cards and related data if it is not made yet
     *
     * @return void
     */
    protected function validateXpaymentsCardsCache()
    {
        if (null === $this->xpaymentsCardsCache) {

            $this->xpaymentsCardsCache = [
                'cards' => [],
                'limit' => 3,
                'limitReached' => false,
                'tokenizationEnabled' => false,
                'tokenizeCardAmount' => '1.00',
            ];

            try {

                if ($this->getXpaymentsCustomerId()) {
                    $response = XPaymentsCloud::getClient()
                        ->doGetCustomerCards($this->getXpaymentsCustomerId());

                    $this->xpaymentsCardsCache['cards'] = $response->cards;

                    foreach ($this->xpaymentsCardsCache['cards'] as &$card) {
                        $card['cssType'] = strtolower($card['type']);
                        $placeholderLength = ('amex' === $card['cssType']) ? 5 : 6;
                        $card['placeholder'] = str_repeat('&#8226;', $placeholderLength);
                        $card['cardNumber'] =
                            $card['first6']
                            . str_repeat('*', $placeholderLength)
                            . $card['last4'];
                        $card['expire'] = sprintf('%s/%s', $card['expireMonth'], $card['expireYear']);
                    }
                } else {
                    $response = XPaymentsCloud::getClient()->doGetTokenizationSettings();
                }

                $this->xpaymentsCardsCache['limit'] = $response->limit;
                $this->xpaymentsCardsCache['limitReached'] = $response->limitReached;
                $this->xpaymentsCardsCache['tokenizationEnabled'] = $response->tokenizationEnabled;
                $this->xpaymentsCardsCache['tokenizeCardAmount'] = $response->tokenizeCardAmount;

            } catch (\Exception $exception) {

                XPaymentsCloud::log($exception->getMessage());
            }
        }
    }

    /**
     * Update default X-Payments saved card
     *
     * @param string $cardId ID of the card
     *
     * @return bool
     */
    public function setXpaymentsDefaultCard($cardId)
    {
        $result = false;

        if ($this->getXpaymentsCustomerId()) {

            try {

                $response = XPaymentsCloud::getClient()
                    ->doSetDefaultCustomerCard($this->getXpaymentsCustomerId(), $cardId);

                $result = (bool)$response->result;

            } catch (\Exception $exception) {

                XPaymentsCloud::log($exception->getMessage());
            }
        }

        return $result;
    }

    /**
     * Remove X-Payments saved card
     *
     * @param string $cardId ID of the card
     *
     * @return bool 
     */
    public function removeXpaymentsCard($cardId)
    {
        $result = false;

        if ($this->getXpaymentsCustomerId()) {

            try {

                $response = XPaymentsCloud::getClient()
                    ->doDeleteCustomerCard($this->getXpaymentsCustomerId(), $cardId);

                $result = (bool)$response->result;

            } catch (\Exception $exception) {

                XPaymentsCloud::log($exception->getMessage());
            }
        }

        return $result;
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getOrders()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->profile = $this;

        return \XLite\Core\Database::getRepo('XLite\Model\Order')->search($cnd);
    }

}
