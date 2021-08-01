<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Customer;

/**
 * Review modify controller
 *
 */
abstract class ACustomer extends \XLite\Module\XC\ThemeTweaker\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Current product cache
     *
     * @var \XLite\Model\Product $product
     */
    protected $product = false;

    /**
     * Runtime cache: review key
     *
     * @var \XLite\Module\XC\Reviews\Model\OrderReviewKey
     */
    protected $reviewKey = null;

    /**
     * Runtime cache: reviewer profile
     *
     * @var \XLite\Model\Profile
     */
    protected $reviewerProfile = null;

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if ($this->product === false) {
            $this->product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
        }

        return $this->product;
    }

    /**
     * Return product id of the current page
     *
     * @return integer
     */
    public function getProductId()
    {
        $productId = parent::getProductId();
        if (empty($productId)) {
            $cellName = \XLite\Module\XC\Reviews\View\ItemsList\Model\Customer\Review::getSessionCellName();
            $cell = (array)\XLite\Core\Session::getInstance()->$cellName;

            $productId = isset($cell['product_id']) ? $cell['product_id'] : null;
        }

        return $productId;
    }

    /**
     * Return true if a valid rkey parameter has been passed
     *
     * @return boolean
     */
    public function isValidReviewKey()
    {
        $result = false;

        if (($rkeys = \XLite\Core\Session::getInstance()->savedReviewKeys) && is_array($rkeys)) {
            foreach ($rkeys as $rkey) {
                if (($reviewKey = $this->getReviewKey($rkey)) && $reviewKey->isValidForProduct($this->getProduct())) {
                    $this->reviewKey = $reviewKey;
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get OrderReviewKey object
     *
     * @param string $rkey rkey value passed in URL OPTIONAL
     *
     * @return \XLite\Module\XC\Reviews\Model\OrderReviewKey
     */
    public function getReviewKey($rkey = null)
    {
        $result = $this->reviewKey;

        if (isset($rkey)) {

            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\OrderReviewKey');

            if ($rkey === (int) $rkey) {
                // Search by ID
                $result = $repo->find($rkey);

            } else {
                // Search by keyValue
                $result = $repo->findOneBy(['keyValue' => $rkey]);
            }
        }

        return $result;
    }

    /**
     * Return current reviewer profile
     *
     * @return \XLite\Model\Profile
     */
    public function getReviewerProfile()
    {
        if (!isset($this->reviewerProfile)) {

            $profile = \XLite\Core\Auth::getInstance()->getProfile();

            if (!$profile && $this->isValidReviewKey()) {
                // If user is not logged in then we check if review key is provided
                $order = $this->getReviewKey()->getOrder();
                if ($order) {
                    $profile = $order->getOrigProfile();
                }
            }

            $this->reviewerProfile = $profile ?: false;
        }

        return $this->reviewerProfile ?: null;
    }

    /**
     * Return TRUE if customer already reviewed product
     *
     * @param \XLite\Model\Product $product
     *
     * @return boolean
     */
    public function isProductReviewedByUser($product = null)
    {
        if (null === $product) {
            $product = $this->getProduct();
        }

        $result = false;

        if (isset($product) && $this->getReviewerProfile()) {
            $result = $product->isReviewedByUser($this->getReviewerProfile());
        }

        return $result;
    }

    /**
     * Return TRUE if customer can add review for product
     *
     * @param \XLite\Model\Product $product
     *
     * @return boolean
     */
    public function isAllowedAddReview($product = null)
    {
        $result = false;

        if ($this->isValidReviewKey()) {
            $result = true;

        } else {

            $result = (bool) $this->getReviewerProfile();

            if ($result && $this->isProductReviewedByUser($product)) {
                $result = false;
            }

            if ($result
                && $this->isPurchasedCustomerOnlyAbleLeaveFeedback()
                && !$this->isUserPurchasedProduct($product)
            ) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Check if add review should request user to login
     *
     * @return bool
     */
    public function isReplaceAddReviewWithLogin()
    {
        return !$this->isValidReviewKey() && !(boolean)$this->getProfile();
    }

    /**
     * Return message instead of 'Add review' button if customer is not allowed to add review
     *
     * @return string
     */
    public function getAddReviewMessage()
    {
        $message = null;

        if (!$this->isValidReviewKey()) {

            if (!$this->getReviewerProfile()) {
                $message = 'Please sign in to add review';
            }

            if (empty($message) && $this->isProductReviewedByUser()) {
                $message = 'You have already reviewed this product';
            }

            if (empty($message) && $this->isPurchasedCustomerOnlyAbleLeaveFeedback()) {
                $message = 'Only customers who purchased this product can leave feedback on this product';
            }
        }

        return static::t($message);
    }

    /**
     * Return TRUE if only customers who purchased this product can leave feedback
     *
     * @return boolean
     */
    public function isPurchasedCustomerOnlyAbleLeaveFeedback()
    {
        $whoCanLeaveFeedback = \XLite\Core\Config::getInstance()->XC->Reviews->whoCanLeaveFeedback;

        return (\XLite\Module\XC\Reviews\Model\Review::PURCHASED_CUSTOMERS == $whoCanLeaveFeedback);
    }

    /**
     * Return true if customer purchased the specified product
     *
     * @param \XLite\Model\Product $product
     *
     * @return boolean
     */
    protected function isUserPurchasedProduct($product)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\OrderItem')
            ->countItemsPurchasedByCustomer($product ? $product->getId() : $this->getProductId(), $this->getReviewerProfile());
    }

    /**
     * Define if review is added by current user
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $entity
     *
     * @return bool
     */
    public function isOwnReview(\XLite\Module\XC\Reviews\Model\Review $entity)
    {
        $result = false;

        $profile = $this->getReviewerProfile();

        if ($profile && $entity->getProfile()) {
            $result = ($entity->getProfile()->getProfileId() === $profile->getProfileId());
        }

        return $result;
    }
}
