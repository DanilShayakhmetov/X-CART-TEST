<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Model;

use XLite\View\FormField\Input\Text as TextInput;
use XLite\View\FormField\Input\Text\FloatInput;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\Text\Integer as IntegerInput;
use XLite\View\FormField\Select\Select2\Category as Select2Category;

/**
 * Coupon
 */
abstract class CouponAbstract extends \XLite\View\Model\AModel
{
    const PARAM_MAX_UNSIGNED_INT_VALUE = 4294967295;

    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'code'             => [
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\Coupons\View\FormField\Code',
            self::SCHEMA_LABEL    => 'Code',
            self::SCHEMA_REQUIRED => true,
        ],
        'comment'          => [
            self::SCHEMA_CLASS          => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL          => 'Comment',
            self::SCHEMA_HELP           => 'This comment will be visible to shop administrators only',
            TextInput::PARAM_MAX_LENGTH => 64,
        ],
        'enabled'          => [
            self::SCHEMA_CLASS => 'XLite\Module\CDev\Coupons\View\FormField\Enabled',
            self::SCHEMA_LABEL => 'Enabled',
        ],
        'type'             => [
            self::SCHEMA_CLASS    => 'XLite\Module\CDev\Coupons\View\FormField\DiscountType',
            self::SCHEMA_LABEL    => 'Discount type',
            self::SCHEMA_REQUIRED => true,
        ],
        'value'            => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL    => 'Discount amount',
            self::SCHEMA_REQUIRED => true,
            FloatInput::PARAM_MIN => 0.01,
            FloatInput::PARAM_MAX => 9999999999.99,
        ],
        'dateRangeBegin'   => [
            self::SCHEMA_CLASS => 'XLite\Module\CDev\Coupons\View\FormField\Date',
            self::SCHEMA_LABEL => 'Active from',
            self::SCHEMA_HELP  => 'Date when customers can start using the coupon',
        ],
        'dateRangeEnd'     => [
            self::SCHEMA_CLASS => 'XLite\Module\CDev\Coupons\View\FormField\Date',
            self::SCHEMA_LABEL => 'Active till',
            self::SCHEMA_HELP  => 'Date when the coupon expires',
        ],
        'totalRangeBegin'  => [
            self::SCHEMA_CLASS            => 'XLite\Module\CDev\Coupons\View\FormField\Total',
            self::SCHEMA_LABEL            => 'Subtotal range (begin)',
            FloatInput::PARAM_MIN         => 0,
            FloatInput::PARAM_ALLOW_EMPTY => false,
            self::SCHEMA_HELP             => 'Minimum order subtotal the coupon can be applied to',
            FloatInput::PARAM_MAX_LENGTH  => 10,
        ],
        'totalRangeEnd'    => [
            self::SCHEMA_CLASS            => 'XLite\Module\CDev\Coupons\View\FormField\Total',
            self::SCHEMA_LABEL            => 'Subtotal range (end)',
            FloatInput::PARAM_MIN         => 0,
            FloatInput::PARAM_ALLOW_EMPTY => false,
            FloatInput::PARAM_MAX_LENGTH  => 10,
            self::SCHEMA_HELP             => 'Maximum order subtotal the coupon can be applied to',
        ],
        'usesLimitCheck'   => [
            self::SCHEMA_CLASS              => 'XLite\View\FormField\Input\Checkbox',
            self::SCHEMA_LABEL              => 'Limit the number of uses',
            AFormField::PARAM_WRAPPER_CLASS => 'input uses-limit-check',
        ],
        'usesLimit'        => [
            self::SCHEMA_CLASS              => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL              => 'The maximum number of uses',
            IntegerInput::PARAM_MIN         => 0,
            IntegerInput::PARAM_MAX         => self::PARAM_MAX_UNSIGNED_INT_VALUE,
            AFormField::PARAM_WRAPPER_CLASS => 'input uses-limit',
            self::SCHEMA_DEPENDENCY         => [
                self::DEPENDENCY_SHOW => [
                    'usesLimitCheck' => true,
                ],
            ],
        ],
        'usesLimitPerUser' => [
            self::SCHEMA_CLASS              => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL              => 'The maximum number of uses per user',
            IntegerInput::PARAM_MIN         => 0,
            IntegerInput::PARAM_MAX         => self::PARAM_MAX_UNSIGNED_INT_VALUE,
            AFormField::PARAM_WRAPPER_CLASS => 'input uses-per-user-limit',
            self::SCHEMA_DEPENDENCY         => [
                self::DEPENDENCY_SHOW => [
                    'usesLimitCheck' => true,
                ],
            ],
        ],
        'singleUse'        => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\Simple',
            self::SCHEMA_LABEL => 'Coupon cannot be combined with other coupons',
        ],
        'specificProducts' => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\YesNo',
            self::SCHEMA_LABEL => 'Valid only for specific products',
        ],
        'categories'       => [
            self::SCHEMA_CLASS              => 'XLite\View\FormField\Select\Select2\Category',
            Select2Category::PARAM_MULTIPLE => true,
            self::SCHEMA_LABEL              => 'Categories',
            self::SCHEMA_HELP               => 'If you want the coupon discount to be applied only to products from specific categories, specify these categories here.',
            self::SCHEMA_DEPENDENCY         => [
                self::DEPENDENCY_HIDE => [
                    'specificProducts' => true,
                ],
            ],
        ],
        'productClasses'   => [
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\ProductClasses',
            self::SCHEMA_LABEL      => 'Product classes',
            self::SCHEMA_HELP       => 'Coupon discount can be limited to these product classes',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_HIDE => [
                    'specificProducts' => true,
                ],
            ],
        ],
        'memberships'      => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Select\Memberships',
            self::SCHEMA_LABEL => 'Memberships',
            self::SCHEMA_HELP  => 'Coupon discount can be limited to customers with these membership levels',
        ],
        'zones'            => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Select\Zones',
            self::SCHEMA_LABEL => 'Address zones (coupons)',
            self::SCHEMA_HELP  => 'The discount is valid for the selected delivery area',
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
     * @return \XLite\Module\CDev\Coupons\Model\Coupon
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Module\CDev\Coupons\Model\Coupon;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\CDev\Coupons\View\Form\Coupon';
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
                \XLite\View\Button\AButton::PARAM_LABEL    => $label,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
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
        $productClasses = isset($data['productClasses']) ? $data['productClasses'] : null;
        $memberships    = isset($data['memberships']) ? $data['memberships'] : null;
        $zones          = isset($data['zones']) ? $data['zones'] : null;
        $categories     = isset($data['categories']) ? $data['categories'] : null;

        $isSpecificProducts = $data['specificProducts'] ?? null;

        unset($data['productClasses'], $data['memberships'], $data['categories'], $data['zones']);

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

        if (empty($data['usesLimitCheck'])) {
            $data['usesLimit']        = 0;
            $data['usesLimitPerUser'] = 0;
        }

        $data['singleUse'] = (empty($data['singleUse']) ? 0 : 1);

        parent::setModelProperties($data);

        /** @var \XLite\Module\CDev\Coupons\Model\Coupon $entity */
        $entity = $this->getModelObject();

        // Product classes
        foreach ($entity->getProductClasses() as $class) {
            $class->getCoupons()->removeElement($entity);
        }
        $entity->clearProductClasses();

        if (false === $isSpecificProducts && is_array($productClasses)) {
            foreach ($productClasses as $id) {
                $class = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')->find($id);
                if ($class) {
                    $entity->addProductClasses($class);
                    $class->addCoupons($entity);
                }
            }
        }

        // Memberships
        foreach ($entity->getMemberships() as $m) {
            $m->getCoupons()->removeElement($entity);
        }
        $entity->clearMemberships();

        if (is_array($memberships)) {
            foreach ($memberships as $id) {
                $m = \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($id);
                if ($m) {
                    $entity->addMemberships($m);
                    $m->addCoupons($entity);
                }
            }
        }

        // Zones
        foreach ($entity->getZones() as $zone) {
            $zone->getCoupons()->removeElement($entity);
        }
        $entity->clearZones();

        if (is_array($zones)) {
            foreach ($zones as $id) {
                $m = \XLite\Core\Database::getRepo('XLite\Model\Zone')->find($id);
                if ($m) {
                    $entity->addZones($m);
                    $m->addCoupons($entity);
                }
            }
        }

        // Categories
        foreach ($entity->getCategories() as $c) {
            $c->getCoupons()->removeElement($entity);
        }
        $entity->clearCategories();

        if (false === $isSpecificProducts && is_array($categories)) {
            foreach ($categories as $id) {
                $c = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($id);
                if ($c) {
                    $entity->addCategories($c);
                    $c->addCoupons($entity);
                }
            }
        }

        if (false === $isSpecificProducts) {
            /** @var \XLite\Model\AEntity $cp */
            foreach ($entity->getCouponProducts() as $cp) {
                \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\CouponProduct')->delete($cp, false);
            }
            $entity->getCouponProducts()->clear();
        }
    }

    protected function rollbackModel()
    {
        /* @var \XLite\Module\CDev\Coupons\Model\Coupon $coupon */
        $coupon = $this->getModelObject();

        foreach ($coupon->getCategories() as $category) {
            $category->getCoupons()->removeElement($coupon);
        }

        foreach ($coupon->getMemberships() as $membership) {
            $membership->getCoupons()->removeElement($coupon);
        }

        foreach ($coupon->getZones() as $zone) {
            $zone->getCoupons()->removeElement($coupon);
        }

        foreach ($coupon->getProductClasses() as $productClass) {
            $productClass->getCoupons()->removeElement($coupon);
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

        [$valid] = $this->getFormField('default', 'dateRangeBegin')->validate();
        if ($valid) {
            $data['dateRangeBegin'] = $this->getFormField('default', 'dateRangeBegin')->getValue();
        }

        [$valid] = $this->getFormField('default', 'dateRangeEnd')->validate();
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
            && 100 < $cell['value']->getValue()
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
            \XLite\Core\TopMessage::addInfo('The coupon has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The coupon has been added');
        }
    }

    /**
     * Prepare usesLimitCheck field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsUsesLimitCheck(array $data)
    {
        $model = $this->getModelObject();
        if ($model && (0 < $model->getUsesLimit() || $model->getUsesLImitPerUser())) {
            $data['isChecked'] = true;
        }

        return $data;
    }

    /**
     * Prepare singleUse field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsSingleUse(array $data)
    {
        $model = $this->getModelObject();
        if (!$model || !$model->getId()) {
            $data['isChecked'] = true;
        }

        return $data;
    }
}
