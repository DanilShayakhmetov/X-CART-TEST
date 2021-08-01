<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Popup;


use XLite\View\AView;

abstract class AutoPopup extends \XLite\View\AView
{
    /**
     * @return string
     */
    abstract protected function getInnerWidgetClass();

    /**
     * @return array
     */
    protected function getInnerWidgetParams()
    {
        return [];
    }

    protected function getDefaultTemplate()
    {
        return 'popup/auto_popup.twig';
    }

    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_JS => [
                'popup/auto_popup.js',
            ],
        ]);
    }

    /**
     * @return array
     */
    protected function getCommentedData()
    {
        return [
            'content'     => $this->getInnerWidgetContent(),
            'popupParams' => $this->getPopupParams(),
        ];
    }

    /**
     * @return array
     */
    protected function getPopupParams()
    {
        return [];
    }

    /**
     * @return AView
     */
    protected function getInnerWidget()
    {
        return $this->getChildWidget(
            $this->getInnerWidgetClass(),
            $this->getInnerWidgetParams()
        );
    }

    /**
     * @return string
     */
    protected function getInnerWidgetContent()
    {
        return $this->getInnerWidget()->getContent();
    }
}