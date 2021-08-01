<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;


class MultipleEmails extends \XLite\View\FormField\Select\Multiple
{
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
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $value = $this->getValue();

        if (is_string($value) && ($data = @unserialize($value)) !== false) {
            if (is_array($data)) {
                $value = [];
                foreach ($data as $email) {
                    $value[$email] = $email;
                }
            }
        }

        return $value;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/multiple_emails.js';

        return $list;
    }
    /**
     * Register JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/multiple_emails.less';

        return $list;
    }

    /**
     * Prepare attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        $attrs = parent::prepareAttributes($attrs);

        $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ') . ' multiple-emails';

        return $attrs;
    }
}