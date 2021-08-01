<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\Model;

/**
 * Profile 
 */
 class Profile extends \XLite\Module\XC\Concierge\Model\Profile implements \XLite\Base\IDecorator
{
    /**
     * Pending zero auth (card setup) status
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $braintree_customer_id = '';

    /**
     * "Cached" list of Braintree credit cards
     */
    protected $braintreeCreditCards = null;

    /**
     * "Cached" list of Braintree credit cards array representation
     */
    protected $braintreeCreditCardsHash = null;

    /**
     *
     * @var string
     *
     * @Column (type="boolean")
     */
    protected $saveCardBoxChecked = false;

    /**
     * Get credit cards from Braintree associated with profile
     *
     * @return array of \Braintree\CreditCard 
     */
    public function getBraintreeCreditCards()
    {
        if (is_null($this->braintreeCreditCards)) {

            $this->braintreeCreditCards = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->getCreditCards($this);
        }

        return $this->braintreeCreditCards;
    }

    /**
     * Get credit cards from Braintree associated with profile via arrays
     * (Braintree's object do not work in templates as is)
     *
     * @return array
     */
    public function getBraintreeCreditCardsHash()
    {
        if (is_null($this->braintreeCreditCardsHash)) {

            $creditCards = $this->getBraintreeCreditCards();

            $this->braintreeCreditCardsHash = array();

            foreach ($creditCards as $card) {

                if ($card instanceOf \Braintree\PayPalAccount) {
                    $hash = array(
                        'email' => $card->email,
                        'type' => 'PayPal',
                    );
                } else {
                    $hash = array(
                        'number' => $card->maskedNumber,
                        'type' => $card->cardType,
                        'expire' => $card->expirationDate,
                    );
                }

                $card->createdAt->setTimezone(\XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->getStoreTimeZone());
                $card->updatedAt->setTimezone(\XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->getStoreTimeZone());

                $hash += array(
                    'token' => $card->token,
                    'image' => $card->imageUrl,
                    'created' => $card->createdAt->format('Y-m-d H:i'),
                    'updated' => $card->updatedAt->format('Y-m-d H:i'),
                    'default' => $card->default,
                );

                if ($hash['created'] == $hash['updated']) {
                    unset($hash['updated']);
                }

                if ($card->default) {
                    $this->braintreeCreditCardDefaultToken = $card->token;
                }

                $this->braintreeCreditCardsHash[] = $hash;
            }
        }

        return $this->braintreeCreditCardsHash;
    }

    /**
     * Set braintree_customer_id
     *
     * @param string $braintreeCustomerId
     * @return Profile
     */
    public function setBraintreeCustomerId($braintreeCustomerId)
    {
        $this->braintree_customer_id = $braintreeCustomerId;
        return $this;
    }

    /**
     * Get braintree_customer_id
     *
     * @return string 
     */
    public function getBraintreeCustomerId()
    {
        return $this->braintree_customer_id;
    }

    /**
     * Get SaveCardBoxChecked
     *
     * @return bool
     */
    public function getSaveCardBoxChecked()
    {
        return $this->saveCardBoxChecked;
    }

    /**
     * Set SaveCardBoxChecked
     *
     * @param bool $status
     * @return \XLite\Model\Profile
     */
    public function setSaveCardBoxChecked($status)
    {
        $this->saveCardBoxChecked = $status;
        return $this;
    }
}
