<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\UserPermissions\View\Model\Profile;

class AdminMain extends \XLite\View\Model\Profile\AdminMain implements \XLite\Base\IDecorator
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionAccess()
    {
        $persistentModel = $this->getModelObject() && $this->getModelObject()->isPersistent();
        if (!$persistentModel && isset($this->accessSchema['roles'])) {
            $this->accessSchema['roles'][self::SCHEMA_COMMENT] = static::t(
                'Attention! You are creating an account with full access. Roles warning',
                [
                    'roles_link' => $this->buildURL('roles'),
                    'kb_link'    => static::t('https://kb.x-cart.com/users/user_roles.html'),
                ]
            );
        }

        return parent::getFormFieldsForSectionAccess();
    }
}