<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Popup;


class ForceChangePassword extends \XLite\View\Popup\AutoPopup
{
    protected function getInnerWidgetClass()
    {
        return '\XLite\View\Model\Profile\ForceChangePassword';
    }

    protected function getInnerWidgetContent()
    {
        return sprintf(
            '<div class="force-change-password-section" data-dialog-class="no-close">%s</div>',
            parent::getInnerWidgetContent()
        );
    }

    protected function getPopupParams()
    {
        return array_merge(parent::getPopupParams(), [
            'closeOnEscape' => false,
        ]);
    }
}