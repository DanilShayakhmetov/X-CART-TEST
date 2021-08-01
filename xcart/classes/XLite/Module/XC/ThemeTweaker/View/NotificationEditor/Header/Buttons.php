<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Header;


use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\ErrorTranslator;
use XLite\Core\Auth;

class Buttons extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/header/buttons.twig';
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getButtonWidgets();
    }


    /**
     * @return Data
     */
    protected function getDataSource()
    {
        return \XLite::getController()->getDataSource();
    }

    /**
     * @return string
     */
    protected function getInterface()
    {
        return \XLite\Core\Request::getInstance()->interface === \XLite::ADMIN_INTERFACE
            ? \XLite::ADMIN_INTERFACE
            : \XLite::CUSTOMER_INTERFACE;
    }

    /**
     * @return array
     */
    protected function getButtonWidgets()
    {
        $list = [];

        if ($this->getDataSource()->isEditable()) {
            if ($this->getDataSource()->isAvailable()) {
                $url = $this->buildURL(
                    'notification',
                    '',
                    [
                        'templatesDirectory' => $this->getDataSource()->getDirectory(),
                        'page'               => $this->getInterface(),
                        'preview'            => true,
                    ]
                );
                $list['preview_template'] = new \XLite\View\Button\Link(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL => 'Preview full email',
                        \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                        \XLite\View\Button\Link::PARAM_BLANK    => true,
                        \XLite\View\Button\Link::PARAM_LOCATION => $url,
                    ]
                );

                $url = $this->buildURL(
                    'notification',
                    'send_test_email',
                    [
                        'templatesDirectory' => $this->getDataSource()->getDirectory(),
                        'page'               => $this->getInterface(),
                    ]
                );
                $list['send_test_email'] = new \XLite\View\Button\Link(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL => static::t('Send to {{email}}', ['email' => Auth::getInstance()->getProfile()->getLogin()]),
                        \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
                        \XLite\View\Button\Link::PARAM_LOCATION => $url,
                    ]
                );
            } else {
                $unavailabilityReason = null;

                foreach ($this->getDataSource()->getUnavailableProviders() as $provider) {
                    $unavailabilityReason = ErrorTranslator::translateAvailabilityError($provider);

                    if ($unavailabilityReason) {
                        break;
                    }
                }

                $list['preview_template'] = new \XLite\View\Button\Tooltip(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL          => 'Preview full email',
                        \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                        \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                        \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => $unavailabilityReason ?: null,
                    ]
                );

                $list['send_test_email'] = new \XLite\View\Button\Tooltip(
                    [
                        \XLite\View\Button\AButton::PARAM_LABEL          => static::t('Send to {{email}}', ['email' => Auth::getInstance()->getProfile()->getLogin()]),
                        \XLite\View\Button\AButton::PARAM_STYLE          => 'action',
                        \XLite\View\Button\AButton::PARAM_DISABLED       => true,
                        \XLite\View\Button\Tooltip::PARAM_BUTTON_TOOLTIP => $unavailabilityReason ?: null,
                    ]
                );
            }
        }

        return $list;
    }
}
