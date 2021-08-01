<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

use XLite\Core\Exception;
use XLite\Core\Mail\Registry;
use XLite\Core\Session;

/**
 * Mailer
 */
class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * @param $templatesDirectory
     *
     * @return bool
     */
    protected static function isAttachPdfInvoice($templatesDirectory)
    {
        return static::isOrderNotification($templatesDirectory)
            && \XLite\Core\Config::getInstance()->NotificationAttachments->attach_pdf_invoices;
    }

    /**
     * @param $templateDirectory
     *
     * @return bool
     */
    public static function isOrderNotification($templateDirectory)
    {
        return in_array(
            $templateDirectory,
            [
                'order_canceled',
                'order_changed',
                'order_created',
                'order_failed',
                'order_processed',
                'order_shipped',
                'order_tracking_information',
            ],
            true
        );
    }

    /**
     * Send created order mail to customer
     *
     * @param string $templatesDirectory
     * @param string $to
     * @param string $interface
     * @param array  $data
     *
     * @return bool
     * @throws Exception
     * @throws \ReflectionException
     */
    public static function sendNotificationPreview($templatesDirectory, $to, $interface, array $data)
    {
        $mail = Registry::createNotification($interface, $templatesDirectory, $data);
        $mail->setLanguageCode(Session::getInstance()->getCurrentLanguage());
        $mail->setTo($to);

        if (!$mail) {
            throw new Exception(sprintf("Undefined email notification: %s/%s", $interface, $templatesDirectory));
        }

        if (
            static::isAttachPdfInvoice($templatesDirectory)
            && !empty($data['order'])
            && $data['order'] instanceof \XLite\Model\Order
        ) {
            $page = new \XLite\View\PdfPage\Invoice();
            $page->setWidgetParams([
                'order'     => $data['order'],
                'interface' => $interface,
            ]);

            $handler = \XLite\Core\Pdf\Handler::getDefault();
            $handler->handlePdfPage($page);
            $document = $handler->output();
            $filename = 'invoice_' . $data['order']->getOrderNumber() . '.pdf';
            $mail->addStringAttachment([$document, $filename, 'base64', 'application/pdf']);
        }

        return $mail->send();
    }
}
