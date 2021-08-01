<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Model;

/**
 * Review view model
 */
class Review extends \XLite\View\Model\AModel
{
    /**
     * @inheritdoc
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $this->schemaDefault = [
            'product'      => [
                self::SCHEMA_CLASS       => 'XLite\View\FormField\Select\Model\ProductSelector',
                self::SCHEMA_LABEL       => 'Product',
                self::SCHEMA_PLACEHOLDER => static::t('SKU or Product name'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'rating'       => [
                self::SCHEMA_CLASS    => 'XLite\Module\XC\Reviews\View\FormField\Input\VoteBar',
                self::SCHEMA_LABEL    => 'Rating',
                self::SCHEMA_REQUIRED => false,
            ],
            'profile'      => [
                self::SCHEMA_CLASS       => 'XLite\View\FormField\Select\Model\ProfileSelector',
                self::SCHEMA_LABEL       => 'Profile',
                self::SCHEMA_PLACEHOLDER => static::t('First name, Last name or E-mail'),
                self::SCHEMA_REQUIRED    => false,
            ],
            'reviewerName' => [
                self::SCHEMA_CLASS       => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL       => 'Reviewer name',
                self::SCHEMA_PLACEHOLDER => static::t('Enter a name to be published with the review'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'review'       => [
                self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
                self::SCHEMA_LABEL    => 'Text of review',
                self::SCHEMA_REQUIRED => false,
            ],
            'response'     => [
                self::SCHEMA_CLASS    => 'XLite\Module\XC\Reviews\View\FormField\Textarea\Response',
                self::SCHEMA_LABEL    => 'Text of response',
                self::SCHEMA_REQUIRED => false,
            ],
        ];

        parent::__construct($params, $sections);

        /** @var \XLite\Module\XC\Reviews\Model\Review $review */
        $review = $this->getModelObject();

        if (
            $review->getRespondent()
            && $review->getRespondent()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
            && !\XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
        ) {
            $this->schemaDefault['response'][self::SCHEMA_ATTRIBUTES] = ['disabled' => 'disabled'];
        }
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/XC/Reviews/form_field/input/email.js';

        return $list;
    }

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    protected function getDefaultModelObject()
    {
        $model = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->find($this->getModelId());

        return $model ?: new \XLite\Module\XC\Reviews\Model\Review;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\Reviews\View\Form\Model\Review';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        if ($this->getModelObject()->getId()) {
            if ($this->isApproved()) {
                $result['submit'] = new \XLite\View\Button\Submit([
                    \XLite\View\Button\AButton::PARAM_LABEL    => 'Update',
                    \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                    \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
                ]);
            } else {
                $result['approve'] = new \XLite\View\Button\Regular([
                    \XLite\View\Button\AButton::PARAM_LABEL    => 'Approve',
                    \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                    \XLite\View\Button\AButton::PARAM_STYLE    => 'action always-enabled',
                    \XLite\View\Button\Regular::PARAM_ACTION   => 'approve',
                ]);
                $result['remove']  = new \XLite\View\Button\Link([
                    \XLite\View\Button\AButton::PARAM_LABEL => 'Remove',
                    \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL(
                        'review',
                        'delete',
                        ['id' => $this->getModelObject()->getId()]
                    ),
                ]);
            }

        } else {
            $result['submit'] = new \XLite\View\Button\Submit([
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Create',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]);
        }

        return $result;
    }

    /**
     * Return whether review is approved
     *
     * @return boolean
     */
    protected function isApproved()
    {
        return \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED == $this->getModelObject()->getStatus();
    }

    /**
     * @inheritdoc
     */
    protected function setModelProperties(array $data)
    {
        /** @var \XLite\Module\XC\Reviews\Model\Review $review */
        $review = $this->getModelObject();

        if ($data['response'] && $review && $review->getResponse() != $data['response']) {
            $review->setRespondent(\XLite\Core\Auth::getInstance()->getProfile());
            $review->setResponseDate(\XLite\Core\Converter::time());
        }

        parent::setModelProperties($data);
    }

    public function isValid()
    {
        return parent::isValid() && $this->getModelObject()->getProduct();
    }
}
