<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Images page controller
 */
abstract class ImagesAbstract extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Resize
     *
     * @var \XLite\Logic\ImageResize\Generator
     */
    protected $imageResizeGenerator = null;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isImageResizeNotFinished()) {
            return static::t('Resizing images...');
        }

        return static::t('Images Settings & Uploading');
    }

    /**
     * Do action 'Update'
     *
     * @throws \Exception
     */
    protected function doActionUpdate()
    {
        $request = \XLite\Core\Request::getInstance();

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Performance',
            'name'     => 'use_dynamic_image_resizing',
            'value'    => (boolean)$request->use_dynamic_image_resizing,
        ]);

        if ($this->isShowUnsharpOption()) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Performance',
                'name'     => 'unsharp_mask_filter_on_resize',
                'value'    => (boolean)$request->unsharp_mask_filter_on_resize,
            ]);
        }

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Performance',
            'name'     => 'resize_quality',
            'value'    => (integer)$request->resize_quality,
        ]);

        if (isset($request->cloud_zoom)) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Layout',
                'name'     => 'cloud_zoom',
                'value'    => (boolean)$request->cloud_zoom,
            ]);
        }

        if (isset($request->cloud_zoom_mode)) {
            \XLite\Core\Layout::getInstance()->setCloudZoomMode($request->cloud_zoom_mode);
        }

        if (isset($request->use_lazy_load)) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Performance',
                'name'     => 'use_lazy_load',
                'value'    => (boolean)$request->use_lazy_load,
            ]);
        }

        $list = new \XLite\View\ItemsList\Model\ImagesSettings();
        $list->processQuick();

        $this->createResizedLogo();
    }

    /**
     * Create resized image for logo
     *
     * @return void
     */
    public function createResizedLogo()
    {
        $logoImage = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getLogo();
        \XLite\Logic\ImageResize\Generator::clearImageSizesCache();
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_VAR . 'images/logo');
        $logoImage->prepareSizes();
    }

    /**
     * Return "Use dynamic image resizing" setting value
     *
     * @return string
     */
    public function getUseDynamicImageResizingValue()
    {
        return \XLite\Core\Config::getInstance()->Performance->use_dynamic_image_resizing;
    }

    /**
     * @return bool
     */
    public function isShowUnsharpOption()
    {
        return \XLite\Core\ImageOperator::getEngineType() === \XLite\Core\ImageOperator::ENGINE_GD;
    }

    /**
     * Return "Unsharp mask filter on resize" setting value
     *
     * @return boolean
     */
    public function getUnsharpMaskFilterOnResizeValue()
    {
        return (boolean)\XLite\Core\Config::getInstance()->Performance->unsharp_mask_filter_on_resize;
    }

    /**
     * Return "Resize quality" setting value
     *
     * @return integer
     */
    public function getResizeQuality()
    {
        return (integer)\XLite\Core\Config::getInstance()->Performance->resize_quality ?: 85;
    }

    /**
     * Return "Lazy load images" setting value
     *
     * @return string
     */
    public function getLazyLoadValue()
    {
        return \XLite\Core\Config::getInstance()->Performance->use_lazy_load;
    }

    // {{{ Image resize methods

    /**
     * Get resize
     *
     * @return \XLite\Logic\ImageResize\Generator
     */
    public function getImageResizeGenerator()
    {
        if (!isset($this->imageResizeGenerator)) {
            $eventName = \XLite\Logic\ImageResize\Generator::getEventName();
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);
            $this->imageResizeGenerator = ($state && isset($state['options']))
                ? new \XLite\Logic\ImageResize\Generator($state['options'])
                : false;
        }

        return $this->imageResizeGenerator;
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isImageResizeNotFinished()
    {
        $eventName = \XLite\Logic\ImageResize\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS]
            )
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getImageResizeCancelFlagVarName());
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionImageResize()
    {
        if (\XLite\Core\ImageOperator::getEngineType() === \XLite\Core\ImageOperator::ENGINE_SIMPLE) {
            \XLite\Core\TopMessage::addError("Image resizing requires libraries");
        } else {
            \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_VAR . 'images/category');
            \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_VAR . 'images/product');
            \XLite\Logic\ImageResize\Generator::run($this->assembleImageResizeOptions());
        }
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleImageResizeOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        return [
            'include' => $request->section,
        ];
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionImageResizeCancel()
    {
        \XLite\Logic\ImageResize\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('The generation of resized images has been stopped.');
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->resize_completed) {
            \XLite\Core\TopMessage::addInfo('The generation of resized images has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('images')
            );

        } elseif ($request->resize_failed) {
            \XLite\Core\TopMessage::addError('The generation of resized images has been stopped.');

            $this->setReturnURL(
                $this->buildURL('images')
            );
        }
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getImageResizeCancelFlagVarName()
    {
        return \XLite\Logic\ImageResize\Generator::getCancelFlagVarName();
    }

    // }}}

    // {{{ Cloud Zoom

    /**
     * Check if cloud zoom enabled
     *
     * @return boolean
     */
    public function getCloudZoomEnabled()
    {
        return \XLite\Core\Layout::getInstance()->getCloudZoomEnabled();
    }

    /**
     * Return cloud zoom mode
     *
     * @return string
     */
    public function getCloudZoomMode()
    {
        return \XLite\Core\Layout::getInstance()->getCloudZoomMode();
    }

    /**
     * Check if cloud zoom supported by skin
     *
     * @return boolean
     */
    public function isCloudZoomAllowed()
    {
        return \XLite\Core\Layout::getInstance()->isCloudZoomAllowed();
    }

    // }}}
}
