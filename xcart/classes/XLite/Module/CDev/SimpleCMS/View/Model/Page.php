<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\Model;

use XLite\Core\Database;
use XLite\View\FileUploader;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\Text\CleanURL;
use XLite\View\FormField\Textarea\Advanced;

/**
 * Page view model
 */
class Page extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = [
        'name'           => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Name',
            self::SCHEMA_REQUIRED => true,
        ],
        'enabled'        => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL    => 'Enabled',
            self::SCHEMA_REQUIRED => false,
        ],
        'cleanURL'       => [
            self::SCHEMA_CLASS                => 'XLite\View\FormField\Input\Text\CleanURL',
            self::SCHEMA_LABEL                => 'CleanURL',
            self::SCHEMA_REQUIRED             => false,
            AFormField::PARAM_LABEL_HELP      => 'Human readable and SEO friendly web address for the page.',
            CleanURL::PARAM_OBJECT_CLASS_NAME => 'XLite\Module\CDev\SimpleCMS\Model\Page',
            CleanURL::PARAM_OBJECT_ID_NAME    => 'id',
            CleanURL::PARAM_ID                => 'cleanurl',
            CleanURL::PARAM_EXTENSION         => \XLite\Model\Repo\CleanURL::CLEAN_URL_DEFAULT_EXTENSION,
        ],
        'body'           => [
            self::SCHEMA_CLASS              => 'XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL              => 'Content',
            self::SCHEMA_REQUIRED           => true,
            self::SCHEMA_TRUSTED_PERMISSION => true,
            Advanced::PARAM_STYLE           => 'page-body-content',
        ],
        'meta_title'     => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Content page title',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_COMMENT  => 'Leave blank to use page name as Page Title.',
        ],
        // todo: rename to meta_tags
        'metaKeywords'   => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Meta keywords',
            self::SCHEMA_REQUIRED => false,
        ],
        'meta_desc_type' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\MetaDescriptionType',
            self::SCHEMA_LABEL    => 'Meta description',
            self::SCHEMA_REQUIRED => false,
        ],
        // todo: rename to meta_desc
        'teaser'         => [
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL      => '',
            self::SCHEMA_REQUIRED   => true,
            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'meta_desc_type' => ['C'],
                ],
            ],
        ],
        'image'          => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\FileUploader\Image',
            self::SCHEMA_LABEL    => 'Open graph image',
            self::SCHEMA_REQUIRED => false,
            FileUploader::PARAM_HAS_ALT => false,
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
            $cleanUrlExt = \XLite\Model\Repo\CleanURL::isStaticPageUrlHasExt() ? \XLite\Model\Repo\CleanURL::CLEAN_URL_DEFAULT_EXTENSION : '';

            if ($this->getModelObject()
                && $this->getModelObject()->getCleanURL()
                && \XLite\Model\Repo\CleanURL::isStaticPageUrlHasExt()
                && !preg_match('/.html$/', $this->getModelObject()->getCleanURL())
            ) {
                $cleanUrlExt = '';
            }

            $data[CleanURL::PARAM_EXTENSION] = $cleanUrlExt;
        }

        return parent::getFieldBySchema($name, $data);
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Module\CDev\SimpleCMS\Model\Page
     */
    protected function getDefaultModelObject()
    {
        $model = $this->getModelId()
            ? Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')->find($this->getModelId())
            : null;

        if (!$model) {
            $model = new \XLite\Module\CDev\SimpleCMS\Model\Page;
            $model->setPosition(Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')->getMaxPosition() + 10);
        }

        return $model;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\Module\CDev\SimpleCMS\View\Form\Model\Page';
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        if ($this->getModelObject()->getId()) {
            $this->schemaDefault['image'][\XLite\View\FormField\Image::PARAM_OBJECT_ID]
                = $this->getDefaultModelObject()->getId();
            if ($this->getDefaultModelObject()->getImage()) {
                $this->schemaDefault['image'][\XLite\View\FormField\Image::PARAM_FILE_OBJECT_ID]
                    = $this->getDefaultModelObject()->getImage()->getId();
                // $this->schemaDefault['image'][\XLite\View\FormField\Image::PARAM_REMOVE_BUTTON] = true;
            }
        }

        return $this->getFieldsBySchema($this->schemaDefault);
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['save'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Save',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            ]
        );

        $result['save_and_close'] = new \XLite\View\Button\Regular(
            [
                \XLite\View\Button\AButton::PARAM_LABEL  => 'Save & Close',
                \XLite\View\Button\AButton::PARAM_STYLE  => 'action',
                \XLite\View\Button\Regular::PARAM_ACTION => 'updateAndClose',
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
        if ('create' !== $this->currentAction) {
            \XLite\Core\TopMessage::addInfo('The page has been updated');

        } else {
            \XLite\Core\TopMessage::addInfo('The page has been added');
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
        if (isset($data['enabled'])) {
            $data['enabled'] = !empty($data['enabled']) ? 1 : 0;
        }

        parent::setModelProperties($data);
    }

    /**
     * Save form fields in session
     *
     * @param mixed $data Data to save
     *
     * @return void
     */
    protected function saveFormData($data)
    {
        unset($data['image']);

        parent::saveFormData($data);
    }

    /**
     * Rollback model if data validation failed
     *
     * @return void
     */
    protected function rollbackModel()
    {
        $urls = $this->getModelObject()->getCleanURLs();
        /** @var \XLite\Model\CleanURL $url */
        foreach ($urls as $url) {
            if (!$url->isPersistent()) {
                Database::getEM()->remove($url);
            }
            Database::getEM()->detach($url);
        }

        parent::rollbackModel();
    }
}
