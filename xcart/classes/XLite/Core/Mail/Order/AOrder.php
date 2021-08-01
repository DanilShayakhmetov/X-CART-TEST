<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Order;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Config;
use XLite\Model\Order;
use XLite\View\AView;

abstract class AOrder extends \XLite\Core\Mail\AMail
{
    use ExecuteCachedTrait;
    private $attachPdfInvoice;

    protected static function defineVariables()
    {
        return [
                'order_number'         => '42',
                'order_total'          => AView::formatPrice(42),
                'recipient_name'       => static::t('John Doe'),
                'first_name'           => static::t('John'),
                'shipping_method_name' => static::t('Shipping method'),
                'payment_method_name'  => static::t('Payment method'),
            ] + parent::defineVariables();
    }

    public function __construct(Order $order)
    {
        parent::__construct();
        $this->appendData([
            'order'          => $order,
            'recipient_name' => $order->getProfile()->getName(),
        ]);
        $this->populateVariables([
            'order_number'         => $order->getOrderNumber(),
            'order_total'          => AView::formatPrice($order->getTotal()),
            'recipient_name'       => $order->getProfile()->getName(),
            'first_name'           => $order->getProfile()->getName(true, true),
            'shipping_method_name' => $order->getShippingMethodName(),
            'payment_method_name'  => $order->getPaymentMethodName(),
        ]);
        $this->setAttachPdfInvoice(Config::getInstance()->NotificationAttachments->attach_pdf_invoices);

        if ($order->getPaymentStatus()) {
            $order->getPaymentStatus()->explicitlyLoadTranslations();
        }
        if ($order->getShippingStatus()) {
            $order->getShippingStatus()->explicitlyLoadTranslations();
        }
    }

    public function send()
    {
        $this->executeCachedRuntime(function () {
            if ($this->isAttachPdfInvoice()) {
                if ($order = $this->getOrder()) {
                    $page = new \XLite\View\PdfPage\Invoice();
                    $page->setWidgetParams([
                        'order'     => $order,
                        'interface' => static::getInterface(),
                    ]);

                    $handler = \XLite\Core\Pdf\Handler::getDefault();

                    $handler->handlePdfPage($page);

                    $document = $handler->output();

                    $filename = 'invoice_' . $order->getOrderNumber() . '.pdf';

                    $this->addStringAttachment([$document, $filename, 'base64', 'application/pdf']);
                }
            }
        }, $this->getOrder() ? $this->getOrder()->getOrderId() : null);

        return parent::send();
    }

    protected function getHashData()
    {
        return array_merge(parent::getHashData(), [$this->getOrderUidForHash()]);
    }

    /**
     * @return int|string
     */
    protected function getOrderUidForHash()
    {
        return $this->getOrder()->getOrderId();
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getData()['order'];
    }

    /**
     * Return AttachPdfInvoice
     *
     * @return bool
     */
    public function isAttachPdfInvoice()
    {
        return $this->attachPdfInvoice;
    }

    /**
     * Set AttachPdfInvoice
     *
     * @param bool $attachPdfInvoice
     *
     * @return $this
     */
    public function setAttachPdfInvoice($attachPdfInvoice)
    {
        $this->attachPdfInvoice = $attachPdfInvoice;
        return $this;
    }
}