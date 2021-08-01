<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Select;

/**
 * Scopes
 */
class Scopes extends \XLite\View\FormField\Select\Multiple
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [ 'modules/CDev/Paypal/form_field/scopes.js' ]
        );
    }

    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [ 'modules/CDev/Paypal/form_field/scopes.less' ]
        );
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'select2/dist/js/select2.min.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return serialize($value);
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     */
    public function setValue($value)
    {
        if (is_string($value) && ($data = @unserialize($value)) !== false) {
            if (is_array($data)) {
                $value = [];
                foreach ($data as $email) {
                    $value[$email] = $email;
                }
            }
        }

        parent::setValue($value);
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'openid'  => 'openid',
            'email'   => 'email',
            'profile' => 'profile',
            'address' => 'address',
        ];
    }

    protected function assembleClasses(array $classes)
    {
        $list = parent::assembleClasses($classes);
        $list[] = 'scopes-select';

        return $list;
    }
}
