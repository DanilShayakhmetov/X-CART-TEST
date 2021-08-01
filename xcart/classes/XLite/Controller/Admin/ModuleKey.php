<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * ModuleKey
 */
class ModuleKey extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Enter license key');
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('view'));
    }

    /**
     * Action of view license view
     *
     * @return void
     */
    protected function doActionView()
    {
    }

    /**
     * Action of license key registration
     *
     * @return void
     */
    protected function doActionRegisterKey()
    {
        $key = trim(\XLite\Core\Request::getInstance()->key);
        $result = \XLite\Core\Marketplace::getInstance()->registerLicense($key);

        if ($result['key'] && $result['action']) {
            $this->setReturnURL(\XLite::getInstance()->getServiceURL('#/' . $result['action']));
        } elseif ($result['alert']) {
            $alert = $result['alert'][0];
            $alertParams = $alert['params'] ? json_decode($alert['params']) : [];

            if ($alert['message'] === 'activate_license_dialog.result.success.core') {
                \XLite\Core\TopMessage::addInfo('X-Cart license key has been successfully verified');

            } elseif ($alert['message'] === 'activate_license_dialog.result.success.module') {
                \XLite\Core\TopMessage::addInfo(
                    'License key has been successfully verified and activated for "{{name}}" module by "{{author}}" author.',
                    [
                        'name' => $alertParams[0],
                        'author' => $alertParams[1],
                    ]
                );

            } elseif ($alert['message'] === 'activate_license_dialog.result.invalid') {
                \XLite\Core\TopMessage::addError(
                    'License registration error: {{error}} ({{code}})',
                    [
                        'error' => $alertParams[2],
                        'code' => $alertParams[1],
                    ]
                );
            }
        }
    }
}
