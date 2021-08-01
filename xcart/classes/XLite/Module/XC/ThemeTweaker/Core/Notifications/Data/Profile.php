<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;


use XLite\Core\Cache\ExecuteCachedTrait;

class Profile extends Provider
{
    use ExecuteCachedTrait;

    public function getData($templateDir)
    {
        return $this->getProfile($templateDir);
    }

    public function getName($templateDir)
    {
        return 'profile';
    }

    public function validate($templateDir, $value)
    {
        if (!$this->findProfileByLogin($value)) {
            return [
                [
                    'code'  => 'profile_nf',
                    'value' => $value,
                ],
            ];
        }

        return [];
    }

    public function isAvailable($templateDir)
    {
        return !!$this->getProfile($templateDir);
    }

    protected function getTemplateDirectories()
    {
        return [
            'profile_created',
            'register_anonymous',
            'access_link',
        ];
    }

    /**
     * @param string $templateDir
     *
     * @return \XLite\Model\Profile|null
     */
    protected function getProfile($templateDir)
    {
        return $this->executeCachedRuntime(function () use ($templateDir) {
            return $this->findProfileByLogin($this->getValue($templateDir))
                ?: \XLite\Core\Database::getRepo('XLite\Model\Profile')->findDumpProfile();
        });
    }

    /**
     * @param $login
     *
     * @return \XLite\Model\Profile|null
     */
    protected function findProfileByLogin($login)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin(
            $login
        );
    }
}