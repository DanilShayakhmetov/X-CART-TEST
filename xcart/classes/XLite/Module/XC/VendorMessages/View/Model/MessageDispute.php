<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Model;

/**
 * Messages (open dispute) view model
 */
class MessageDispute extends \XLite\View\Model\AModel
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $schema = [
            'body' => [
                self::SCHEMA_CLASS                                          => 'XLite\View\FormField\Textarea\Simple',
                self::SCHEMA_LABEL                                          => static::t('Reason'),
                self::SCHEMA_REQUIRED                                       => true,
                \XLite\View\FormField\Textarea\ATextarea::PARAM_ROWS        => 4,
            ],
        ];

        return $this->getFieldsBySchema($schema);
    }

    /**
     * @inheritdoc
     */
    public function getModelId()
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModelObject()
    {
        return $this->getCurrentThreadOrder()->buildNewMessage(
            \XLite\Core\Auth::getInstance()->getProfile()
        );
    }

    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XC\VendorMessages\View\Form\Dispute';
    }

    /**
     * @inheritdoc
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Open dispute',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function addDataSavedTopMessage()
    {
        \XLite\Core\TopMessage::addInfo('A dispute has been opened successfully');
    }

    /**
     * @inheritdoc
     */
    protected function performActionCreate()
    {
        $this->getModelObject()->openDispute();

        return parent::performActionCreate();
    }

}
