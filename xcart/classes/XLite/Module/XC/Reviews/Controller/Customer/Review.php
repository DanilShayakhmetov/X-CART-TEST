<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Customer;

/**
 * Review modify controller
 */
class Review extends \XLite\Controller\Customer\ACustomer
{
    /**
     * review
     *
     * @var \XLite\Module\XC\Reviews\Model\Review
     */
    protected $review;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return ($this->getReview() && $this->getReview()->isPersistent())
            ? static::t('Edit your review')
            : static::t('Add your own review');
    }

    /**
     * Return allowed return targets
     *
     * @return array
     */
    protected function getAllowedReturnTargets()
    {
        return [
            'product',
            'product_reviews'
        ];
    }

    /**
     * Get return URL
     *
     * @return string
     */
    public function getReturnURL()
    {
        $url = parent::getReturnURL();

        if (\XLite\Core\Request::getInstance()->action) {
            $target = in_array($this->getReturnTarget(), $this->getAllowedReturnTargets())
                ? $this->getReturnTarget()
                : 'product';

            $params = ['product_id' => $this->getProductId()];

            $widget = \XLite\Core\Request::getInstance()->widget;
            if ($widget) {
                $params['widget'] = $widget;
            }

            $url = $this->buildURL($target, '', $params);

            if ($target === 'product') {
                $url .= '#product-details-tab-reviews';
            }
        }

        return $url;
    }

    /**
     * Return current product Id
     *
     * @param boolean $getFromReview Get from review flag OPTIONAL
     *
     * @return integer
     */
    public function getProductId($getFromReview = true)
    {
        $productId = parent::getProductId();

        if (empty($productId) && $getFromReview) {
            $review = $this->getReview();

            if ($review) {
                $productId = $review->getProduct()->getProductId();
            }
        }

        return $productId;
    }

    /**
     * Return review
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    public function getReview()
    {
        $id = $this->getId();
        $review = null;

        if ($id) {
            $review = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->find($id);
        }

        if (!$review || !$this->isOwnReview($review)) {
            $review = new \XLite\Module\XC\Reviews\Model\Review;

            $profile = $this->getReviewerProfile();

            $review->setRating(\XLite\Module\XC\Reviews\Model\Review::MAX_RATING);
            $review->setReviewerName($this->getProfileField('reviewerName'));
            $review->setProfile($profile);
        }

        return $review;
    }

    /**
     * Return review Id
     *
     * @return integer
     */
    public function getId()
    {
        $id = \XLite\Core\Request::getInstance()->id;

        if (empty($id)) {
            $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId(false));
            $profile = $this->getReviewerProfile();
            $review = $product ? $product->getReviewAddedByUser($profile) : null;

            if ($review) {
                $id = $review->getId();
            }
        }

        return $id;
    }

    /**
     * Return target to return
     *
     * @return string
     */
    public function getReturnTarget()
    {
        $result = \XLite\Core\Request::getInstance()->return_target;

        if ($result === 'quick_look') {
            $result = 'product';
        }

        return $result;
    }

    /**
     * Return field value from current profile
     *
     * @param string $field Field
     *
     * @return string
     */
    public function getProfileField($field)
    {
        $value = '';
        $profile = $this->getReviewerProfile();
        if ($profile) {
            switch ($field) {
                case 'reviewerName':
                    if (0 < $profile->getAddresses()->count()) {
                        $value = $profile->getAddresses()->first()->getName();
                    }
                    break;

                case 'email':
                    $value = $profile->getLogin();
                    break;

                default:
            }
        }

        return $value;
    }

    /**
     * Alias
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    protected function getEntity()
    {
        return $this->getReview();
    }

    /**
     * Get editable fields
     *
     * @return array
     */
    protected function getEditableFields()
    {
        return [
            'rating',
            'reviewerName',
            'review',
            'email',
        ];
    }

    /**
     * Get posted data
     *
     * @return array
     */
    protected function getRequestData()
    {
        $data = \XLite\Core\Request::getInstance()->getData();

        if (!is_array($data)) {
            $data = [];
        }

        foreach ($data as $k => $v) {
            if (!in_array($k, $this->getEditableFields(), true)) {
                unset($data[$k]);
            }
        }

        return $data;
    }

    /**
     * Rate product
     *
     * @return void
     */
    protected function doActionRate()
    {
        $this->doActionModify();

        $this->setPureAction(true);
    }

    /**
     * Modify model
     *
     * @return void
     */
    protected function doActionModify()
    {
        if ($this->getId()) {
            $this->doActionUpdate();

        } else {
            $this->doActionCreate();
        }
    }

    /**
     * Create new model
     *
     * @return void
     */
    protected function doActionCreate()
    {
        $data = $this->getRequestData();

        $profile = $this->getReviewerProfile();

        $review = new \XLite\Module\XC\Reviews\Model\Review();

        $review->map($data);
        $review->setProfile($profile);

        if (!$review->getReviewerName()) {
            $review->setReviewerName($this->getProfileField('reviewerName'));
        }

        $status = (false === \XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews || !$review->getReview())
            ? \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED
            : \XLite\Module\XC\Reviews\Model\Review::STATUS_PENDING;

        $review->setStatus($status);

        if (
            $this->isValidReviewKey()
            && $profile
            && ($reviewKey = $this->getReviewKey())
            && $reviewKey->getOrder()
            && $reviewKey->getOrder()->getOrigProfile()
            && $reviewKey->getOrder()->getOrigProfile()->getProfileId() == $profile->getProfileId()
        ) {
            // Set relation between review key and submitted review
            $review->setReviewKey($reviewKey);
            $reviewKey->addReviews($review);
        }

        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
        $review->setProduct($product);
        $product->addReviews($review);

        \XLite\Core\Database::getEM()->flush();

        $message = 'Thank your for sharing your opinion with us!';

        if (!$review->getReview()) {
            $message = 'Your product rating is saved. Thank you!';
        }

        \XLite\Core\TopMessage::addInfo(
            static::t($message)
        );
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $data = $this->getRequestData();

        $review = $this->getReview();

        $status = (false === \XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews || empty($data['review']))
            ? \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED
            : \XLite\Module\XC\Reviews\Model\Review::STATUS_PENDING;

        $review->setStatus($status);
        $review->map($data);

        if ($status === \XLite\Module\XC\Reviews\Model\Review::STATUS_PENDING) {
            $review->setIsNew(true);
        }

        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\TopMessage::addInfo(
            static::t('Your review has been updated. Thank your for sharing your opinion with us!')
        );
    }
}
