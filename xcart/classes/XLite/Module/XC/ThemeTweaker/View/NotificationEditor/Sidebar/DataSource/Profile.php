<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;


use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\View\AView;

class Profile extends AView implements DataSource
{
    private $data;

    static public function isApplicable(Data $data)
    {
        return in_array(
            $data->getDirectory(),
            static::getTemplateDirectories(),
            true
        );
    }

    public function __construct(Data $data)
    {
        $this->data = $data;
        parent::__construct([]);
    }

    static public function buildNew(Data $data)
    {
        return new static($data);
    }

    protected static function getTemplateDirectories()
    {
        return [
            'profile_created',
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/data_source/profile/body.twig';
    }

    /**
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return isset($this->data->getData()['profile'])
            ? $this->data->getData()['profile']
            : null;
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->getProfile()
            ? $this->getProfile()->getLogin()
            : '';
    }
}