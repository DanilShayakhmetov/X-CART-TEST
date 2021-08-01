<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Form\ItemsList;


class Conversation extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'conversation';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['id'] = \XLite::getController()->getConversationId();

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' conversation-messages');
    }
}