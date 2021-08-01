<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Customer;

/**
 * Product page controller
 */
abstract class Product extends \XLite\Module\XC\ThemeTweaker\Controller\Customer\Product implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function handleRequest()
    {
        if ($rkey = \XLite\Core\Request::getInstance()->rkey) {
            // rkey is passed in parameters
            if ($reviewKey = $this->detectReviewKey($rkey)) {
                $rkeys = \XLite\Core\Session::getInstance()->savedReviewKeys;
                if (!$rkeys) {
                    $rkeys = array();
                }
                $rkeys[] = $reviewKey->getId();
                \XLite\Core\Session::getInstance()->savedReviewKeys = array_unique($rkeys);
            }
            $this->redirect($this->buildURL('product', '', array(
                'product_id' => $this->getProductId(),
            )) . '#product-details-tab-reviews');
        }

        parent::handleRequest();
    }

    /**
     * Return review key object
     *
     * @param string $rkey rkey parameter value
     *
     * @return \XLite\Module\XC\Reviews\Model\OrderReviewKey
     */
    protected function detectReviewKey($rkey)
    {
        $reviewKey = $this->getReviewKey($rkey);

        if ($reviewKey && !$reviewKey->getFirstClickDate()) {
            // Save date of first click on link with rkey
            $reviewKey->setFirstClickDate(\XLite\Core\Converter::time());
            \XLite\Core\Database::getEM()->flush($reviewKey);
        }

        return $reviewKey;
    }
}
