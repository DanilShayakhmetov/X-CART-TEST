<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\Model\Product;

/**
 * Product tab view model
 */
class Tab extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'enabled'    => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\YesNo',
            self::SCHEMA_LABEL    => 'Enabled',
            self::SCHEMA_REQUIRED => false,
        ],
        'name'       => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Name',
            self::SCHEMA_REQUIRED => true,
        ],
        'contentTab' => [
            self::SCHEMA_CLASS                                   => '\XLite\Module\XC\CustomProductTabs\View\FormField\Textarea\Description',
            self::SCHEMA_LABEL                                   => 'Content',
            self::SCHEMA_REQUIRED                                => true,
            self::SCHEMA_TRUSTED_PERMISSION                      => true,
            \XLite\View\FormField\Textarea\Advanced::PARAM_STYLE => 'product-description',
        ],
        'brief_info' => [
            self::SCHEMA_CLASS => '\XLite\Module\XC\CustomProductTabs\View\FormField\Textarea\BriefInfo',
            self::SCHEMA_LABEL => 'Brief info',
            self::SCHEMA_HELP  => 'Brief info help',
        ],
    ];

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CustomProductTabs/product_tab/style.css';

        return $list;
    }

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getModelId()
    {
        return \XLite\Core\Request::getInstance()->tab_id;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\XC\CustomProductTabs\Model\Product\Tab
     */
    protected function getDefaultModelObject()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\Tab');
        $model = $this->getModelId()
            ? $repo->find($this->getModelId())
            : null;

        return $model ?: new \XLite\Module\XC\CustomProductTabs\Model\Product\Tab;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\XC\CustomProductTabs\View\Form\Model\Product\Tab';
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

        $result['save_and_close'] = new \XLite\View\Button\Regular(
            [
                \XLite\View\Button\AButton::PARAM_LABEL  => 'Save & Close',
                \XLite\View\Button\AButton::PARAM_STYLE  => 'action',
                \XLite\View\Button\Regular::PARAM_ACTION => 'updateProductTabAndClose',
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
            \XLite\Core\TopMessage::addInfo('The product tab has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The product tab has been added');
        }
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
        $data['content'] = $data['contentTab'];
        $this->prepareObjectForMapping()->setProduct($this->getProduct());
        $this->getProduct()->addTabs($this->prepareObjectForMapping());

        parent::setModelProperties($data);
    }

    /**
     * Change model object value
     *
     * @param string $name Object value name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        if ('contentTab' == $name) {
            $name = 'content';
        }

        return parent::getModelObjectValue($name);
    }
}
