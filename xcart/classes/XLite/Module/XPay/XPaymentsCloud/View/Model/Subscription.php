<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Model;

/**
 * Subscription view model
 */
class Subscription extends \XLite\View\Model\AModel
{
    /**
     * Shema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'failedAttempts'  => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Failed attempts',
            self::SCHEMA_REQUIRED => false,
        ],
        'successfulAttempts' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Successful attempts',
            self::SCHEMA_REQUIRED => false,
        ],
        'startDate'    => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Start date',
            self::SCHEMA_REQUIRED => false,
        ],
        'plannedDate'  => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Planned date',
            self::SCHEMA_REQUIRED => false,
        ],
        'actualDate'     => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Actual date',
            self::SCHEMA_REQUIRED => false,
        ],
        'status'       => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Status',
            self::SCHEMA_REQUIRED => false,
        ],
        'type'         => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Type',
            self::SCHEMA_REQUIRED => false,
        ],
        'number'       => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Number',
            self::SCHEMA_REQUIRED => false,
        ],
        'period'       => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Period',
            self::SCHEMA_REQUIRED => false,
        ],
        'reverse'      => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Reverse',
            self::SCHEMA_REQUIRED => false,
        ],
        'periods'      => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL    => 'Periods',
            self::SCHEMA_REQUIRED => false,
        ],
        'fee'          => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL    => 'Fee',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

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
     * This object will be used if another one is not pased
     *
     * @return \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription
     */
    protected function getDefaultModelObject()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XPay\XPaymentsCloud\Model\Subscription');

        $model = $this->getModelId()
            ? $repo->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XPay\XPaymentsCloud\View\Form\Model\Subscription';
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $label = $this->getModelObject()->getId() ? 'Update' : 'Create';

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => $label,
                \XLite\View\Button\AButton::PARAM_STYLE => 'action',
            ]
        );

        return $result;
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ('create' != $this->currentAction) {
            \XLite\Core\TopMessage::addInfo('The subscription has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The subscription has been added');
        }
    }

}
