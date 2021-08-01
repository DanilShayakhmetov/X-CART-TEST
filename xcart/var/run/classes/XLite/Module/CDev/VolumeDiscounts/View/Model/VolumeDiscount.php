<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\Model;

use XLite\View\FormField\Input\Text\FloatInput;

/**
 * Volume discount
 */
class VolumeDiscount extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'type'            => [
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\SelectDiscountType',
            self::SCHEMA_LABEL    => 'Discount type',
            self::SCHEMA_REQUIRED => true,
        ],
        'value'           => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL    => 'Discount amount',
            self::SCHEMA_REQUIRED => true,
            FloatInput::PARAM_MIN => 0.01,
        ],
        'dateRangeBegin'  => [
            self::SCHEMA_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\Date',
            self::SCHEMA_LABEL => 'Active fromF',
            self::SCHEMA_HELP  => 'Date when customers can start using the volume discount',
        ],
        'dateRangeEnd'    => [
            self::SCHEMA_CLASS => 'XLite\Module\CDev\VolumeDiscounts\View\FormField\Date',
            self::SCHEMA_LABEL => 'Active tillF',
            self::SCHEMA_HELP  => 'Date when the volume discount expires',
        ],
        'subtotalRangeBegin' => [
            self::SCHEMA_CLASS            => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL            => 'Subtotal',
            FloatInput::PARAM_MIN         => 0,
            FloatInput::PARAM_ALLOW_EMPTY => false,
            self::SCHEMA_HELP             => 'Minimum order subtotal to which the volume discount can be applied',
        ],
        'membership'     => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Select\Membership',
            self::SCHEMA_LABEL => 'Membership',
            self::SCHEMA_HELP  => 'Volume discount can be limited to customers with this membership level',
        ],
        'zones'     => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Select\Zones',
            self::SCHEMA_LABEL => 'Zones',
            self::SCHEMA_HELP  => 'Volume discount can be limited to customers with these address zones',
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
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\CDev\VolumeDiscounts\View\Form\VolumeDiscount';
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
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        return $result;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $zones = isset($data['zones']) ? $data['zones'] : null;

        unset($data['zones']);

        if (!empty($data['dateRangeBegin'])) {
            $data['dateRangeBegin'] = mktime(
                23,
                59,
                59,
                date('n', $data['dateRangeBegin']),
                date('j', $data['dateRangeBegin']),
                date('Y', $data['dateRangeBegin'])
            );
        }

        if (!empty($data['dateRangeEnd'])) {
            $data['dateRangeEnd'] = mktime(
                23,
                59,
                59,
                date('n', $data['dateRangeEnd']),
                date('j', $data['dateRangeEnd']),
                date('Y', $data['dateRangeEnd'])
            );
        }

        parent::setModelProperties($data);

        $volumeDiscount = $this->getModelObject();

        // Zones
        foreach ($volumeDiscount->getZones() as $z) {
            $z->getVolumeDiscounts()->removeElement($volumeDiscount);
        }
        $volumeDiscount->clearZones();

        if (is_array($zones)) {
            foreach ($zones as $id) {
                $z = \XLite\Core\Database::getRepo('XLite\Model\Zone')->find($id);
                if ($z) {
                    $volumeDiscount->addZone($z);
                    $z->addVolumeDiscount($volumeDiscount);
                }
            }
        }
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        $volumeDiscount = $this->getModelObject();

        foreach ($volumeDiscount->getZones() as $zone) {
            $zone->getVolumeDiscounts()->removeElement($volumeDiscount);
        }

        parent::rollbackModel();
    }

    /**
     * Prepare posted data for mapping to the object
     *
     * @return array
     */
    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();

        list($valid) = $this->getFormField('default', 'dateRangeBegin')->validate();
        if ($valid) {
            $data['dateRangeBegin'] = $this->getFormField('default', 'dateRangeBegin')->getValue();
        }

        list($valid) = $this->getFormField('default', 'dateRangeEnd')->validate();
        if ($valid) {
            $data['dateRangeEnd'] = $this->getFormField('default', 'dateRangeEnd')->getValue();
        }

        return $data;
    }

    /**
     * Check if field is valid and (if needed) set an error message
     *
     * @param array  $data    Current section data
     * @param string $section Current section name
     *
     * @return void
     */
    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        $cell = $data[self::SECTION_PARAM_FIELDS];

        if (!$this->errorMessages
            && isset($cell['type'], $cell['value'])
            && '%' === $cell['type']->getValue()
            && 100 <  $cell['value']->getValue()
        ) {
            $this->addErrorMessage('value', 'Discount cannot be more than 100%', $data);
        }
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ('create' !== $this->currentAction) {
            \XLite\Core\TopMessage::addInfo('The volume discount has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The volume discount has been added');
        }
    }

    /**
     * Return list of form error messages
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        $test = '';
        if (!isset($this->errorMessages)) {
            $this->errorMessages = parent::getErrorMessages();

            if (!$this->errorMessages) {
                $currentDiscount = $this->getModelObject();
                $currentDiscountZones = !$currentDiscount->getZones() || is_array($currentDiscount->getZones())
                    ? $currentDiscount->getZones()
                    : $currentDiscount->getZones()->toArray();

                $similarDiscounts = \XLite\Core\Database::getRepo('XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount')->findSimilarDiscounts($currentDiscount);

                foreach ($similarDiscounts as $similarDiscount) {
                    if ($similarDiscount->getZones()->toArray() == $currentDiscountZones) {
                        $this->errorMessages[] = $currentDiscount->getId()
                            ? $this->getErrorMessageTextForDiscountUpdating()
                            : $this->getErrorMessageTextForDiscountAdding();
                        break;
                    }
                }
            }
        }

        return $this->errorMessages;
    }

    /**
     * Get error message text for update action
     *
     * @return string
     */
    protected function getErrorMessageTextForDiscountUpdating() {
        return static::t('Could not update the discount because another discount already exists for the specified subtotal range, membership level, date range and shipping zones');
    }

    /**
     * Get error message text for add action
     *
     * @return string
     */
    protected function getErrorMessageTextForDiscountAdding() {
        return static::t('Could not add the discount because another discount already exists for the specified subtotal range, membership level, date range and shipping zones');
    }
}
