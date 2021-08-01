<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;

use XLite\Core\PreloadedLabels\ProviderInterface;
use XLite\Model\Order;

/**
 * Order info
 */
abstract class InfoAbstract extends \XLite\View\AView implements ProviderInterface
{
    /**
     * Shipping modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $shippingModifier;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'order/page/info.js';
        $list[] = 'select_address/controller.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'order/page/info.less';
        $list[] = 'address/order/style.css';
        $list[] = 'select_address/style.css';

        return $list;
    }

    public function getPreloadedLanguageLabels()
    {
        return [
            'Current price for the selected configuration and quantity: X' => static::t('Current price for the selected configuration and quantity: X')
        ];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/page/info.twig';
    }

    /**
     * Get surcharge totals
     *
     * @return array
     */
    protected function getSurchargeTotals()
    {
        return $this->getOrderForm()->getSurchargeTotals();
    }

    // {{{ Content helpers

    /**
     * @return Order
     */
    protected function getOrder()
    {
        return \XLite::getController()->getOrder();
    }

    /**
     * Get order formatted creation date
     *
     * @return string
     */
    protected function getOrderDate()
    {
        return $this->formatTime($this->getOrder()->getDate());
    }

    /**
     * Get profile URL
     *
     * @return string
     */
    protected function getProfileURL()
    {
        return \XLite\Core\Converter::buildURL(
            'profile',
            '',
            array('profile_id' => $this->getOrder()->getOrigProfile()->getProfileId())
        );
    }

    /**
     * Get profile name
     *
     * @return string
     */
    protected function getProfileName()
    {
        $profile = $this->getOrder()->getProfile();
        $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        if (!$address) {
            $profile->getAddresses()->first();
        }

        return $address ? func_htmlspecialchars($address->getName()) : $profile->getLogin();
    }

    /**
     * Get profile email
     *
     * @return string
     */
    protected function getProfileEmail()
    {
        return $this->getOrder()->getProfile()->getLogin();
    }

    /**
     * Check - has profile separate modification page or not
     *
     * @return boolean
     */
    protected function hasProfilePage()
    {
        $order = $this->getOrder();

        return $order->getOrigProfile()
            && $order->getOrigProfile()->getProfileId() !== $order->getProfile()->getProfileId();
    }

    /**
     * Check if need to show customer login
     *
     * @return bool
     */
    protected function isShowProfileLogin()
    {
        return true;
    }

    /**
     * Get order formatted subtotal
     *
     * @return string
     */
    protected function getOrderSubtotal()
    {
        $order = $this->getOrder();

        return $this->formatPriceHTML($order->getSubtotal(), $order->getCurrency());
    }

    /**
     * Get order formatted total
     *
     * @return string
     */
    protected function getOrderTotal()
    {
        $order = $this->getOrder();

        return $this->formatPriceHTML($order->getTotal(), $order->getCurrency());
    }

    /**
     * Get shipping cost
     *
     * @return float
     */
    protected function getShippingCost()
    {
        return $this->getOrder()->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);
    }

    /**
     * Get membership
     *
     * @return \XLite\Model\Membership
     */
    protected function getMembership()
    {
        return $this->getOrder()->getProfile()
            ? $this->getOrder()->getProfile()->getMembership()
            : null;
    }

    // }}}

    // {{{ Items content helpers

    /**
     * Get columns span
     *
     * @return integer
     */
    protected function getColumnsSpan()
    {
        return 4 + count($this->getOrder()->getItemsExcludeSurcharges());
    }

    /**
     * Get item description block columns count
     *
     * @return integer
     */
    protected function getItemDescriptionCount()
    {
        return 3;
    }

    /**
     * Get surcharge container attribuites
     *
     * @param string $type      Surcharge type
     * @param array  $surcharge Surcharge
     *
     * @return array
     */
    protected function getSurchargeAttributes($type, array $surcharge)
    {
        return array(
            'class' => array(
                'order-modifier',
                strtolower($type) . '-modifier',
                strtolower($surcharge['code']) . '-code-modifier',
                ($this->isAutoSurcharge($surcharge) ? 'ctrl-auto' : 'ctrl-manual'),
            ),
        );
    }

    /**
     * Check - customer notes block is visible or not
     *
     * @return boolean
     */
    protected function isCustomerNotesVisible()
    {
        return (bool)$this->getOrder()->getNotes();
    }

    /**
     * Get list of actual payment sums (authorized, captured, refunded)
     *
     * @return array
     */
    protected function getPaymentTransactionSums()
    {
        return $this->getOrder()->getPaymentTransactionSums();
    }

    /**
     * Returns true if order has payment transaction sums greater than zero
     *
     * @return boolean
     */
    protected function hasPaymentTransactionSums()
    {
        return 0 < array_sum($this->getPaymentTransactionSums());
    }

    /**
     * Check - history box is visible or not
     *
     * @return boolean
     */
    protected function isHistoryVisible()
    {
        $result = false;

        if ($this->getOrder()) {
            $result = (bool) \XLite\Core\Database::getRepo('XLite\Model\OrderHistoryEvents')->findOneBy(array('order' => $this->getOrder()));
        }

        return $result;
    }

    // }}}

    /**
     * Check - display AntiFraud module advertisment or not
     *
     * @return boolean
     */
    protected function isDisplayAntiFraudAd()
    {
        // The advertisment is switched off in the module
        return true;
    }

    /**
     * AntiFraud module link at marketplace
     *
     * @return string
     */
    protected function getAntiFraudAdLink()
    {
        return 'https://www.x-cart.com/extensions/addons/antifraud.html?utm_source=xcart5&utm_medium=link&utm_campaign=xcart5_antifraud_link';
    }

    /**
     * @return bool
     */
    protected function isDisplayBackorderWarning()
    {
        return $this->getOrder()->getShippingStatusCode() === Order\Status\Shipping::STATUS_NEW_BACKORDERED;
    }

    /**
     * @return string
     */
    protected function getBackorderWarningText()
    {
        if ($backorderCompetitiorsText = $this->getBackorderCompetitorsText()) {
            $warningText = static::t('This order was placed at the same time as {{orders}}; as a result, some items were out of stock at the time of order placement.', [
                'orders' => $this->getBackorderCompetitorsText(),
                'kb_link' => 'https://kb.x-cart.com/orders/understanding_x-cart_order_statuses.html',
            ]);

        } else {
            $warningText = static::t('Some items were out of stock at the time of order placement.', [
                'kb_link' => 'https://kb.x-cart.com/orders/understanding_x-cart_order_statuses.html',
            ]);
        }

        return $warningText;
    }

    /**
     * @return string
     */
    protected function getBackorderCompetitorsText()
    {
        return $this->getOrder()->getBackorderCompetitors()->isEmpty()
            ? ''
            : implode(', ', array_map(function (Order $order) {
                return sprintf(
                    '<a href="%s">%s</a>',
                    $this->buildFullURL('order', '', [
                        'order_number' => $order->getOrderNumber(),
                    ]),
                    '#' . $order->getOrderNumber()
                    );
            }, $this->getOrder()->getBackorderCompetitors()->toArray()));
    }
}
