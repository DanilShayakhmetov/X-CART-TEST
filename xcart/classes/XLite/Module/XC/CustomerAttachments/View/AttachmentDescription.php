<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\View;

use \XLite\Module\XC\CustomerAttachments\View\FormField\Input\Text\FileSizeInteger;
use \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments;
use \XLite\Core\Converter;

/**
 * Attachment description widget
 */
class AttachmentDescription extends \XLite\View\AView
{
    /**
     * Widget params
     */
    const PARAM_ORDER_ITEM = 'orderItem';
    const PARAM_IS_DETAIL_PAGE = 'isDetailPage';

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CustomerAttachments/attachment_description.twig';
    }

    /**
     * Return file size precision
     *
     * @return int
     */
    protected function getPrecision()
    {
        return FileSizeInteger::PARAM_VALUE_E;
    }

    /**
     * Get human readable allowed file size
     *
     * @return string
     */
    public function getAllowedSizeHumanReadable()
    {
        $allowedSize = round(
            CustomerAttachments::getAllowedSize() / Converter::MEGABYTE,
            $this->getPrecision()
        );

        return "{$allowedSize} MB";
    }

    /**
     * Get human readable POST size limit
     *
     * @return string
     */
    protected function getPostSizeLimitHumanReadable()
    {
        return Converter::convertShortSizeToHumanReadable(ini_get('post_max_size'));
    }

    /**
     * Return POST size limit
     *
     * @return string
     */
    protected function getPostSizeLimit()
    {
        return Converter::convertShortSize(ini_get('post_max_size'));
    }

    /**
     * Return max_file_uploads
     *
     * @return string
     */
    protected function getMaxFileUploads()
    {
        return ini_get('max_file_uploads');
    }

    /**
     * Get allowed file extensions string
     *
     * @return string
     */
    public function getAllowedExtensionsString()
    {
        $config = \XLite\Core\Config::getInstance()->XC->CustomerAttachments;

        return preg_replace('/[^,\w+]/', '', $config->extensions);
    }

    /**
     * Get allowed to attach files quantity
     *
     * @return integer
     */
    public function getAllowedQuantity()
    {
        return $this->getItem()
            ? CustomerAttachments::getAllowedQuantity($this->getItem())
            : \XLite\Core\Config::getInstance()->XC->CustomerAttachments->quantity;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, '\XLite\Model\OrderItem'),
            self::PARAM_IS_DETAIL_PAGE => new \XLite\Model\WidgetParam\TypeBool('Is uses on product detail page', false),
        );
    }

    /**
     * Get order item
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(self::PARAM_ORDER_ITEM);
    }
}