<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View;

/**
 * Popup X-Payments transaction info
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PopupXpaymentsInfo extends \XLite\View\AView
{
    /**
     * Hash for transactions
     */
    protected $transactions = null;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('popup_xpayments_info'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XPay/XPaymentsCloud/order/xpayments_info';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return formatted time
     *
     * @param string $time
     *
     * @return string
     */
    public function getTime($time)
    {
        return \XLite\Core\Converter::getInstance()->formatTime(intval($time));
    }

    /**
     * Get SDK Client
     *
     * @return \XPaymentsCloud\Client
     */
    protected function getXpaymentsClient()
    {
        return \XLite\Module\XPay\XPaymentsCloud\Main::getClient();
    }

    /**
     * Get X-Payments transaction details
     *
     * @return boolean
     */
    public function getXpaymentsInfo()
    {
        if (null === $this->transactions) {

            $xpid = \XLite\Core\Request::getInstance()->xpid;

            $this->transactions = array();

            if (
                $xpid
                && $this->getXpaymentsClient()
            ) {

                try {

                    $this->transactions = $this->getXpaymentsClient()
                        ->doGetInfo($xpid)
                        ->transactions;

                } catch (\Exception $exception) {

                    \XLite\Logger::getInstance()->logCustom('XPaymentsCloud', $exception->getMessage());
                }
            }
        }

        return $this->transactions;
    }
}
