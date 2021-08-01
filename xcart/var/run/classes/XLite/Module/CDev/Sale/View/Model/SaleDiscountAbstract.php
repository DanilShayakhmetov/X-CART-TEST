<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Model;

use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\Text\CleanURL;

/**
 * Sale discount
 */
abstract class SaleDiscountAbstract extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'name' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Name',
            self::SCHEMA_REQUIRED => true,
            \XLite\View\FormField\Input\Text::PARAM_MAX_LENGTH => 255,
        ],
        'enabled' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\YesNo',
            self::SCHEMA_LABEL    => 'Enabled',
        ],
        'showInSeparateSection' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\YesNo',
            self::SCHEMA_LABEL    => 'List sale products in a separate section',
        ],
        'cleanURL' => [
            self::SCHEMA_CLASS                => 'XLite\View\FormField\Input\Text\CleanURL',
            self::SCHEMA_LABEL                => 'CleanURL',
            self::SCHEMA_REQUIRED             => false,
            AFormField::PARAM_LABEL_HELP      => 'Human readable and SEO friendly web address for the page.',
            CleanURL::PARAM_OBJECT_CLASS_NAME => 'XLite\Module\CDev\Sale\Model\SaleDiscount',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'showInSeparateSection' => true,
                ],
            ],
        ],
        'meta_title'     => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Discount page title',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_COMMENT  => 'Leave blank to use discount name as Page Title.',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'showInSeparateSection' => true,
                ],
            ],
        ],
        'meta_tags' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Meta keywords',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'showInSeparateSection' => true,
                ],
            ],
        ),
        'meta_desc_type' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\MetaDescriptionType',
            self::SCHEMA_LABEL    => 'Meta description',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'showInSeparateSection' => true,
                ],
            ],
        ),
        'meta_desc' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL    => '',
            self::SCHEMA_REQUIRED => true,
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array (
                    'showInSeparateSection' => true,
                    'meta_desc_type' => array('C'),
                ),
            ),
        ),
        'value' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Percent',
            self::SCHEMA_LABEL    => 'Discount amount',
            self::SCHEMA_REQUIRED => true,
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MIN => 0.01,
            \XLite\View\FormField\Input\Text\FloatInput::PARAM_MAX => 100
        ],
        'dateRangeBegin' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Date',
            self::SCHEMA_LABEL    => 'Active from',
        ],
        'dateRangeEnd' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Date',
            self::SCHEMA_LABEL    => 'Active till',
        ],
        'specificProducts' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\YesNo',
            self::SCHEMA_LABEL    => 'Valid only for specific products',
        ],
        'categories' => [
            self::SCHEMA_CLASS                                            => 'XLite\View\FormField\Select\Select2\Category',
            \XLite\View\FormField\Select\Select2\Category::PARAM_MULTIPLE => true,
            self::SCHEMA_LABEL                                            => 'Categories',
            self::SCHEMA_HELP                                             => 'If you want the sale discount to be applied only to products from specific categories, specify these categories here.',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_HIDE => [
                    'specificProducts' => true,
                ],
            ],
        ],
        'productClasses' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\ProductClasses',
            self::SCHEMA_LABEL    => 'Product classes',
            self::SCHEMA_HELP     => 'The sale discount will be limited to product classes specified here',
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_HIDE => [
                    'specificProducts' => true,
                ],
            ],
        ],
        'memberships' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Memberships',
            self::SCHEMA_LABEL    => 'Memberships',
            self::SCHEMA_HELP     => 'The sale discount will be limited to customers with membership levels specified here',
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
     * @return \XLite\Module\CDev\Sale\Model\SaleDiscount
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscount')->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Module\CDev\Sale\Model\SaleDiscount;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\CDev\Sale\View\Form\SaleDiscount';
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

        $result['recalculateQD'] = $this->getRecalculateQDWidget();

        return $result;
    }

    /**
     * Get "recalculateQD" widget
     *
     * @return \XLite\View\AView
     */
    protected function getRecalculateQDWidget()
    {
        return $this->getWidget(
            [
                'template' => 'modules/CDev/Sale/recalculate_qd_link/body.twig',
                'link'     => $this->buildURL('cache_management')
            ],
            '\XLite\View\Button\Link'
        );
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
        $memberships = isset($data['memberships']) ? $data['memberships'] : null;
        $categories = isset($data['categories']) ? $data['categories'] : null;

        $isSpecificProducts = $data['specificProducts'] ?? null;

        unset($data['productClasses'], $data['memberships'], $data['categories']);

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

        /** @var \XLite\Module\CDev\Sale\Model\SaleDiscount $entity */
        $entity = $this->getModelObject();

        // Product classes
        foreach ($entity->getProductClasses() as $class) {
            $class->getSaleDiscounts()->removeElement($entity);
        }
        $entity->clearProductClasses();

        if (false === $isSpecificProducts && is_array($productClasses)) {
            foreach ($productClasses as $id) {
                $class = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')->find($id);
                if ($class) {
                    $entity->addProductClasses($class);
                    $class->addSaleDiscount($entity);
                }
            }
        }

        // Memberships
        foreach ($entity->getMemberships() as $m) {
            $m->getSaleDiscounts()->removeElement($entity);
        }
        $entity->clearMemberships();

        if (is_array($memberships)) {
            foreach ($memberships as $id) {
                $m = \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($id);
                if ($m) {
                    $entity->addMemberships($m);
                    $m->addSaleDiscount($entity);
                }
            }
        }

        // Categories
        foreach ($entity->getCategories() as $c) {
            $c->getSaleDiscounts()->removeElement($entity);
        }
        $entity->clearCategories();

        if (false === $isSpecificProducts && is_array($categories)) {
            foreach ($categories as $id) {
                $c = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($id);
                if ($c) {
                    $entity->addCategories($c);
                    $c->addSaleDiscount($entity);
                }
            }
        }

        if (false === $isSpecificProducts) {
            /** @var \XLite\Model\AEntity $discountProduct */
            foreach ($entity->getSaleDiscountProducts() as $discountProduct) {
                \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscountProduct')->delete($discountProduct, false);
            }
            $entity->getSaleDiscountProducts()->clear();
        }
    }

    protected function rollbackModel()
    {
        /* @var \XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount */
        $saleDiscount = $this->getModelObject();

        foreach ($saleDiscount->getCategories() as $category) {
            $category->getSaleDiscounts()->removeElement($saleDiscount);
        }

        foreach ($saleDiscount->getMemberships() as $membership) {
            $membership->getSaleDiscounts()->removeElement($saleDiscount);
        }

        foreach ($saleDiscount->getProductClasses() as $productClass) {
            $productClass->getSaleDiscounts()->removeElement($saleDiscount);
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
            && isset($cell['value'])
            && 100 < $cell['value']->getValue()
        ) {
            $this->addErrorMessage('value', 'Discount cannot be more than 100%', $data);
        }
    }

    /**
     * getFieldBySchema
     *
     * @param string $name Field name
     * @param array  $data Field description
     *
     * @return AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        if ('meta_title' === $name) {
            $data[static::SCHEMA_PLACEHOLDER] = static::t('Default');
        }

        if ('cleanURL' === $name) {
            $cleanUrlExt = \XLite\Model\Repo\CleanURL::isSaleDiscountUrlHasExt() ? \XLite\Model\Repo\CleanURL::CLEAN_URL_DEFAULT_EXTENSION : '';

            if ($this->getModelObject()
                && $this->getModelObject()->getCleanURL()
                && \XLite\Model\Repo\CleanURL::isSaleDiscountUrlHasExt()
                && !preg_match('/.html$/', $this->getModelObject()->getCleanURL())
            ) {
                $cleanUrlExt = '';
            }

            $data[CleanURL::PARAM_EXTENSION] = $cleanUrlExt;
        }

        return parent::getFieldBySchema($name, $data);
    }

    /**
     * Add top message
     *
     * @return void
     */
    protected function addDataSavedTopMessage()
    {
        if ('create' !== $this->currentAction) {
            \XLite\Core\TopMessage::addInfo('The sale discount has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The sale discount has been added');
        }
    }
}
