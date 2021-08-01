<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema\Complex;

/**
 * Order schema
 */
class Order implements \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
{
    /**
     * Shop language code
     * @var string
     */
    protected $language;

    public function __construct()
    {
        $this->language = \XLite\Core\Config::getInstance()->General->default_language;
    }

    /**
     * Convert model
     *
     * @param \XLite\Model\AEntity  $model            Entity
     * @param boolean               $withAssociations Convert with associations
     *
     * @return array
     */
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations)
    {
        $shippingCost = $model->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);

        $items = array();
        foreach ($model->getItems() as $item) {
            $items[] = $this->convertItemModel($item);
        }

        $profile = $model->getOrigProfile() ?: $model->getProfile();
        $orderProfile = $model->getProfile() ?: $model->getOrigProfile();
        $baddress = $orderProfile
            ? $orderProfile->getBillingAddress()
            : null;
        $saddress = $orderProfile
            ? $orderProfile->getShippingAddress()
            : null;
        $sinfo = $saddress
            ? $this->convertAddressModel($saddress)
            : null;
        $binfo = $baddress
            ? $this->convertAddressModel($baddress)
            : null;

        $paymentMethodName = $model->getPaymentMethodName();
        if (!$paymentMethodName) {
            $t = $model->getPaymentTransactions()->first();
            if ($t) {
                $paymentMethodName = $t->getPaymentMethod()
                    ? $t->getPaymentMethod()->getTitle()
                    : $t->getMethodLocalName();
            }
        }

        $otherSurcharges = [];
        $excludeSurcharges = [
            \XLite\Model\Base\Surcharge::TYPE_TAX,
            \XLite\Model\Base\Surcharge::TYPE_DISCOUNT,
            \XLite\Model\Base\Surcharge::TYPE_SHIPPING,
        ];
        foreach ($model->getSurcharges() as $surcharge) {
            if (in_array($surcharge->getType(), $excludeSurcharges, true)) {
                continue;
            }
            $otherSurcharges[$surcharge->getCode()] = [
                'name'      => $surcharge->getName(),
                'value'     => $surcharge->getValue(),
            ];
        }

        $transactions = [];

        foreach ($model->getPaymentTransactions() as $transaction) {
            $transactions[] = $this->convertTransactionModel(
                $transaction
            );
        }

        $details = [];

        foreach ($model->getDetails() as $detail) {
            $details[] = $this->convertDetailModel($detail);
        }

        return array(
            'orderId'        => $model->getOrderId(),
            'orderNumber'    => $model->getOrderNumber(),
            'subtotal'       => $model->getTotal() - $shippingCost,
            'total'          => $model->getTotal(),
            'shippingCost'   => $shippingCost,
            'paymentFee'     => 0,
            'taxAmount'      => $this->calculateTaxAmount($model),
            'discountValue'  => $model->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT),
            'otherSurcharges'=> $otherSurcharges,
            'currency'       => $model->getCurrency()->getCode(),
            'items'          => $items,
            'orderDate'      => date('c', $model->getDate()),
            'updateDate'     => date('c', $model->getLastRenewDate()),
            'trackingNumber' => $model->getTrackingNumbers()->first() ? $model->getTrackingNumbers()->first()->getValue() : '',
            'customerNotes'  => $model->getNotes(),
            'adminNotes'     => $model->getAdminNotes(),
            'coupon'         => [],
            'customerInfo'   => $this->convertProfileModel(
                $profile,
                $baddress ? $baddress->getFirstname() : '',
                $baddress ? $baddress->getLastname() : ''
            ),
            'billingInfo'    => $binfo,
            'shippingInfo'   => $sinfo,
            'paymentMethod'  => $paymentMethodName,
            'paymentStatus'  => $model->getPaymentStatusCode(),
            'shippingMethod' => $model->getShippingMethodName(),
            'shippingStatus' => $model->getShippingStatusCode(),
            'transactions'   => $transactions,
            'details'        => $details,
        );
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected function calculateTaxAmount(\XLite\Model\Order $order)
    {
        $surcharges = $order->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_TAX);
        $total = 0;

        foreach ($surcharges as $s) {
            $total += $s->getValue();
        }

        return round($total, 2);
    }

    /**
     * Convert order item model
     *
     * @param  \XLite\Model\OrderItem $item Order item
     * @return array
     */
    protected function convertItemModel(\XLite\Model\OrderItem $item)
    {
        $translation = $item->getProduct()->getSoftTranslation($this->language);
        $attributes = [];
        foreach ($item->getAttributeValues() as $av) {
            $attributes[$av->getActualName()] = $av->getActualValue();
        }

        return array(
            'sku'              => $item->getSku(),
            'productId'        => $item->getProduct()->getProductId(),
            'name'             => $item->getName(),
            'description'      => $translation ? $translation->getDescription() : '',
            'shortDescription' => $translation ? $translation->getBriefDescription() : '',
            'attributes'       => $attributes,
            'price'            => $item->getPrice(),
            'weight'           => $item->getProduct()->getWeight(),
            'quantity'         => $item->getAmount(),
            'subtotal'         => $item->getSubtotal(),
            'total'            => $item->getTotal(),
            'releaseDate'      => $item->getProduct()->getArrivalDate() ? date('c', $item->getProduct()->getArrivalDate()) : null,
            'URL'              => $item->getProduct()->getFrontURL(),
            'enabled'          => $item->getProduct()->getEnabled(),
            'freeShipping'     => $item->getProduct()->getFreeShipping(),
        );
    }

    /**
     * Convert address model
     * @param  \XLite\Model\Address $address Address
     * @return array
     */
    protected function convertAddressModel(\XLite\Model\Address $address)
    {
        $result = [];

        $backwardsCompatDict = [
            'street'        => 'address',
            'state_id'      => 'state',
            'custom_state'  => 'state',
            'country_code'  => 'country',
        ];

        foreach ($address->getAddressFields() as $field) {
            if (!$field->getAddressField()->getEnabled()) {
                continue;
            }

            $key = $field->getAddressField()->getServiceName();
            if (isset($backwardsCompatDict[$key])) {
                $key = $backwardsCompatDict[$key];
            }

            $result[$key] = $field->getValue();
        }

        if ($address->getCountry() && $address->getCountry()->hasStates()) {
            $result['state'] = $address->getState()->getCode()
                ?: $address->getState()->getState();
        }

        $result['isDefaultShippingAddress'] = $address->getIsShipping();
        $result['isDefaultBillingAddress'] = $address->getIsBilling();

        return $result;
    }

    /**
     * Convert profile model
     *
     * @param  \XLite\Model\Profile $profile Profile
     * @param  string $firstname First name
     * @param  string $lastname Last name
     * @return array
     */
    protected function convertProfileModel(\XLite\Model\Profile $profile, $firstname, $lastname)
    {
        return array(
            'profileId'  => $profile->getProfileId(),
            'isAnonymous'=> $profile->getAnonymous(),
            'login'      => $profile->getLogin(),
            'email'      => $profile->getLogin(),
            'status'     => $profile->getStatus(),
            'isAdmin'    => $profile->isAdmin(),
            'addedDate'  => $profile->getAdded() ? date('c', $profile->getAdded()) : 0,
            'membership' => $profile->getMembership() ? $profile->getMembership()->getName() : '',
            'firstname'  => $firstname,
            'lastname'   => $lastname,
        );
    }

    /**
     * Convert transaction model
     *
     * @param  \XLite\Model\Payment\Transaction $translation Transaction
     * @return array
     */
    protected function convertTransactionModel(\XLite\Model\Payment\Transaction $translation)
    {
        return [
            'id'            => $translation->getPublicId(),
            'type'          => $translation->getType(),
            'value'         => $translation->getValue(),
            'status'        => $translation->getStatus(),
            'human_status'  => $translation->getReadableStatus(),
            'method'        => $translation->getMethodName(),
            'note'          => $translation->getNote(),
        ];
    }

    /**
     * Convert detail model
     *
     * @param  \XLite\Model\OrderDetail $detail Detail
     * @return array
     */
    protected function convertDetailModel(\XLite\Model\OrderDetail $detail)
    {
        return [
            'code'      => $detail->getDetailId(),
            'label'     => $detail->getLabel(),
            'value'     => $detail->getValue(),
        ];
    }

    /**
     * Prepare input
     *
     * @param array $data Data
     *
     * @return array
     */
    public function prepareInput(array $data)
    {
        return [ true, $data ];
    }

    /**
     * Preload data
     *
     * @param \XLite\Model\AEntity $entity Product
     * @param array                $data   Data
     *
     * @return void
     */
    public function preloadData(\XLite\Model\AEntity $entity, array $data)
    {
    }
}
