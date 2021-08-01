<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * File uploader
 */
abstract class FileUploaderAbstract extends \XLite\View\AView
{
    use \XLite\View\Base\ViewListsFallbackTrait;

    /**
     * Widget param names
     */
    const PARAM_OBJECT             = 'object';
    const PARAM_OBJECT_ID          = 'objectId';
    const PARAM_MESSAGE            = 'message';
    const PARAM_HELP_MESSAGE       = 'helpMessage';
    const PARAM_MAX_WIDTH          = 'maxWidth';
    const PARAM_MAX_HEIGHT         = 'maxHeight';
    const PARAM_IS_IMAGE           = 'isImage';
    const PARAM_IS_TEMPORARY       = 'isTemporary';
    const PARAM_NAME               = 'fieldName';
    const PARAM_MULTIPLE           = 'multiple';
    const PARAM_POSITION           = 'position';
    const PARAM_IS_VIA_URL_ALLOWED = 'isViaUrlAllowed';
    const PARAM_IS_REMOVABLE       = 'removable';
    const PARAM_HAS_ALT            = 'hasAlt';

    /**
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_CSS][] = $this->getDir() . '/style.less';
        $list[static::RESOURCE_JS] = array_merge($list[static::RESOURCE_JS], static::getVueLibraries());
        $list[static::RESOURCE_JS][] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            $this->getDir() . '/additional_style.less'
        ];
    }

    /**
     * Return field value
     *
     * @return \XLite\Model\Base\Storage
     */
    protected function getObject()
    {
        $result = $this->getParam(static::PARAM_OBJECT);

        return is_object($result) ? $result : null;
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    protected function getObjectId()
    {
        $result = intval($this->getParam(static::PARAM_OBJECT_ID));
        if (
            !$result
            && !$this->isTemporary()
            && $this->hasFile()
        ) {
            $result = $this->getObject()->getId();
        }

        return $result;
    }

    /**
     * Return message
     *
     * @return string
     */
    protected function getMessage()
    {
        return $this->getParam(static::PARAM_MESSAGE);
    }

    /**
     * @return string
     */
    protected function getErrorMessageDefault()
    {
        return static::t('No file was uploaded');
    }

    /**
     * Return message
     *
     * @return string
     */
    protected function getHelp()
    {
        return $this->getParam(static::PARAM_HELP_MESSAGE);
    }

    /**
     * Checking widget is multiple or not
     *
     * @return boolean
     */
    protected function isMultiple()
    {
        return $this->getParam(static::PARAM_MULTIPLE);
    }

    /**
     * Return position
     *
     * @return integer
     */
    protected function getPosition()
    {
        return $this->getParam(static::PARAM_POSITION) || !$this->hasFile()
            ? $this->getParam(static::PARAM_POSITION)
            : 0;
    }

    /**
     * Return max width
     *
     * @return integer
     */
    protected function getMaxWidth()
    {
        return $this->getParam(static::PARAM_MAX_WIDTH);
    }

    /**
     * Return max height
     *
     * @return integer
     */
    protected function getMaxHeight()
    {
        return $this->getParam(static::PARAM_MAX_HEIGHT);
    }

    /**
     * Return field name
     *
     * @return string
     */
    protected function getName()
    {
        $name = $this->getParam(static::PARAM_NAME);
        if ($this->getParam(static::PARAM_MULTIPLE)) {
            $index = $this->getParam(static::PARAM_OBJECT_ID);
            if (!$index) {
                if ($this->getObject()) {
                    $index = (integer)$this->getObject()->getId();
                    if ($this->getParam(static::PARAM_IS_TEMPORARY) && $index > 0) {
                        $index = '-' . $index;
                    }
                }
            }
            $name .= '[' . $index . ']';
        }

        return $name;
    }

    protected function getVModel()
    {
        $name = $this->getParam(static::PARAM_NAME);
        $name = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
        $parts = explode('.', $name);
        $name = '';

        foreach ($parts as $part) {
            if (!strlen($name)) {
                $name = $part;
                continue;
            }

            if (is_numeric($part) && (integer)$part == $part) {
                $name .= '[' . $part . ']';
            } else {
                $name .= '.' . $part;
            }
        }

        if ($this->getParam(static::PARAM_MULTIPLE)) {
            $index = $this->getParam(static::PARAM_OBJECT_ID);
            if (!$index) {
                if ($this->getObject()) {
                    $index = (integer)$this->getObject()->getId();
                    if ($this->getParam(static::PARAM_IS_TEMPORARY) && $index > 0) {
                        $index = '-' . $index;
                    }
                }
            }
            $name .= '[' . $index . ']';
        }

        return $name;
    }

    /**
     * Return preview
     *
     * @return string
     */
    protected function getPreview()
    {
        if ($this->isImage() && $this->hasFile()) {
            $viewer = new \XLite\View\Image([
                'image'        => $this->getObject(),
                'maxWidth'     => $this->getParam(static::PARAM_MAX_WIDTH),
                'maxHeight'    => $this->getParam(static::PARAM_MAX_HEIGHT),
                'alt'          => '',
                'centerImage'  => true,
                'useBlurBg'    => false,
                'useTimestamp' => $this->isFavicon()
            ]);

            return $viewer->getContent();
        }

        return '';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_NAME               => new \XLite\Model\WidgetParam\TypeString('Name', 'file'),
            static::PARAM_OBJECT             => new \XLite\Model\WidgetParam\TypeObject('Object', null),
            static::PARAM_OBJECT_ID          => new \XLite\Model\WidgetParam\TypeInt('Object Id', 0),
            static::PARAM_MESSAGE            => new \XLite\Model\WidgetParam\TypeString('Message', ''),
            static::PARAM_HELP_MESSAGE       => new \XLite\Model\WidgetParam\TypeString('Help message', ''),
            static::PARAM_MAX_WIDTH          => new \XLite\Model\WidgetParam\TypeInt('Max. width', 120),
            static::PARAM_MAX_HEIGHT         => new \XLite\Model\WidgetParam\TypeInt('Max. height', 120),
            static::PARAM_IS_IMAGE           => new \XLite\Model\WidgetParam\TypeBool('Is image', false),
            static::PARAM_IS_TEMPORARY       => new \XLite\Model\WidgetParam\TypeBool('Is temporary', false),
            static::PARAM_MULTIPLE           => new \XLite\Model\WidgetParam\TypeBool('Multiple', false),
            static::PARAM_POSITION           => new \XLite\Model\WidgetParam\TypeInt('Position', 0),
            static::PARAM_IS_VIA_URL_ALLOWED => new \XLite\Model\WidgetParam\TypeInt('Is ViaUrl allowed', true),
            static::PARAM_IS_REMOVABLE       => new \XLite\Model\WidgetParam\TypeBool('Is removable', true),
            static::PARAM_HAS_ALT            => new \XLite\Model\WidgetParam\TypeBool('Has alt', true),
        ];
    }

    /**
     * Check widget has file or not
     *
     * @return boolean
     */
    protected function hasFile()
    {
        $object = $this->getObject();

        return $object && $object->getId();
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);
    }

    /**
     * Check object is image or not
     *
     * @return boolean
     */
    protected function isImage()
    {
        $object = $this->getObject();

        return $this->getParam(static::PARAM_IS_IMAGE)
            || ($object && $object->isImage());
    }

    /**
     * Check object is temporary or not
     *
     * @return boolean
     */
    protected function isTemporary()
    {
        return $this->hasFile()
            && (
                $this->getParam(static::PARAM_IS_TEMPORARY)
                || $this->getObject() instanceof \XLite\Model\TemporaryFile
            );
    }

    /**
     * Check object is removable or not
     *
     * @return boolean
     */
    protected function isRemovable()
    {
        return $this->getParam(static::PARAM_IS_REMOVABLE) || $this->getParam(static::PARAM_MULTIPLE);
    }

    /**
     * Check widget has multiple selector or not
     *
     * @return boolean
     */
    protected function hasMultipleSelector()
    {
        return $this->isMultiple() && !$this->hasFile() && !$this->getMessage();
    }

    /**
     * Return link
     *
     * @return string
     */
    protected function getLink()
    {
        $link = '#';

        if ($this->hasView()) {
            $link = $this->getObject()->getFrontURL();

            if ($this->isFavicon()) {
                $link .= '?' . time();
            }
        }

        return $link;
    }

    /**
     * Is ia url allowed
     *
     * @return boolean
     */
    protected function isViaUrlAllowed()
    {
        return $this->getParam(static::PARAM_IS_VIA_URL_ALLOWED);
    }

    /**
     * Check widget has view or not
     *
     * @return boolean
     */
    protected function hasView()
    {
        return !$this->getMessage()
            && $this->hasFile()
            && $this->isImage();
    }

    /**
     * Return alt
     *
     * @return string
     */
    protected function getAlt()
    {
        return $this->getParam(static::PARAM_IS_IMAGE) && method_exists($this->getObject(), 'getAlt') ? $this->getObject()->getAlt() : '';
    }

    /**
     * Check widget has alt or not
     *
     * @return boolean
     */
    protected function hasAlt()
    {
        return $this->getParam(static::PARAM_HAS_ALT)
               && $this->isModelHasAlt();
    }

    /**
     * Check widget has alt or not
     *
     * @return boolean
     */
    protected function isModelHasAlt()
    {
        $result = $this->getParam(static::PARAM_IS_IMAGE) && method_exists($this->getObject(), 'getAlt');

        if (
            !$result
            && $this->getParam(static::PARAM_IS_IMAGE)
            && $this->getObject() instanceof \XLite\Model\TemporaryFile
        ) {
            $result = $this->getObject()->isImage() && $this->getObject()->getSize();
        }

        return $result;
    }

    /**
     *Return icon style
     *
     * @return string
     */
    protected function getIconStyle()
    {
        $result = 'fa ';
        $result .= ($this->getMessage() || $this->hasFile()) ? 'fa-bars' : 'fa-plus';

        return $result;
    }

    /**
     *Return div style
     *
     * @return string
     */
    protected function getDivStyle()
    {
        $result = 'dropdown file-uploader';

        if ($this->isMultiple() && !$this->hasMultipleSelector()) {
            $result .= ' item';
        }

        if ($this->getMessage() || $this->hasFile()) {
            $result .= ' solid';
        }

        return $result;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'file_uploader';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check favicon or not
     *
     * @return boolean
     */
    protected function isFavicon()
    {
        return $this->fieldName == 'favicon';
    }
}
