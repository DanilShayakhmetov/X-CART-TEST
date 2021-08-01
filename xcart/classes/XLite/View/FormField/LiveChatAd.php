<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;


class LiveChatAd extends \XLite\View\FormField\AFormField
{

    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
    }

    protected function getFieldTemplate()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return 'https://www.livechatinc.com/help/integrating-live-chat-with-your-x-cart-store/';
    }

    protected function getDefaultTemplate()
    {
        return 'form_field/label/live_chat_ad.twig';
    }
}
