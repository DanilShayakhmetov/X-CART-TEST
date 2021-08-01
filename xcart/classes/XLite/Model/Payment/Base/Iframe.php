<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment\Base;

/**
 * Abstract credit card, web-based (iframe) processor
 */
abstract class Iframe extends \XLite\Model\Payment\Base\CreditCard
{
    /**
     * Payment widget data 
     * 
     * @var array
     */
    protected $paymentWidgetData = array();

    /**
     * Get iframe data
     *
     * @return string|array URL or POST data
     */
    abstract protected function getIframeData();

    /**
     * Get iframe additional attributes
     *
     * @return array
     */
    protected function getIframeAdditionalAttributes()
    {
        return [];
    }

    /**
     * Get return request owner transaction or null
     *
     * @return \XLite\Model\Payment\Transaction|void
     */
    public function getReturnOwnerTransaction()
    {
        return null;
    }

    /**
     * Get payment widget data 
     * 
     * @return array
     */
    public function getPaymentWidgetData()
    {
        return $this->paymentWidgetData;
    }

    /**
     * Get iframe form URL
     *
     * @return string
     */
    protected function getIframeFormURL()
    {
    }

    /**
     * Get iframe size 
     * 
     * @return array
     */
    protected function getIframeSize()
    {
        return array(600, 400);
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $this->transaction->createBackendTransaction($this->getInitialTransactionType());

        $data = $this->getIframeData();

        if (isset($data)) {

            list($width, $height) = $this->getIframeSize();

            \XLite\Core\Session::getInstance()->iframePaymentData = array(
                'width'  => $width,
                'height' => $height,
                'src'    => is_array($data) ? $this->assembleFormIframe($data) : $this->assembleURLIframe($data),
                'additional_attributes' => $this->getIframeAdditionalAttributes(),
            );

            $status = static::SEPARATE;

        } else {
            $this->setDetail(
                'iframe_data_error',
                'Payment processor \'' . get_called_class() . '\' did not assemble service data successfully.'
            );
            $status = static::FAILED;
            $this->transaction->setNote('Payment is failed');
        }

        return $status;
    }

    /**
     * Assemble form-based iframe 
     * 
     * @param array $data Form elements
     *  
     * @return string
     */
    protected function assembleFormIframe(array $data)
    {
        $content = new \XLite\Model\IframeContent;
        $content->setData($data);
        $content->setUrl($this->getIframeFormURL());

        \XLite\Core\Database::getEM()->persist($content);
        \XLite\Core\Database::getEM()->flush();

        return \XLite\Core\Converter::buildURL('iframe_content', '', array('id' => $content->getId()));
    }

    /**
     * Assemble URL-based iframe 
     * 
     * @param string $data Iframe URL
     *  
     * @return string
     */
    protected function assembleURLIframe($data)
    {
        return $data;
    }
}
