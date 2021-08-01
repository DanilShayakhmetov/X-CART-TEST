<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;


/**
 * Trait title for profile page tabs controllers
 */
trait ProfilePageTitleTrait
{
    /**
     * Return title string
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return string
     */
    public function getTitleString($profile)
    {
        return $profile && $profile->getLogin()
                ? $profile->getLogin()
                    . ($this->getTitleProfileName($profile) ? ' (' . $this->getTitleProfileName($profile) . ')' : '')
                : '';
    }

    /**
     * Get profile name or profile type
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return string
     */
    protected function getTitleProfileName($profile)
    {
        $name = '';

        if ($profile) {
            $name = $profile->getName(false);

            if (!$name) {
                $name = $profile->isAdmin()
                    ? static::t('Administrator')
                    : ($profile->getAnonymous()
                        ? static::t('Anonymous Customer')
                        : static::t('Registered Customer')
                    );
            }
        }

        return $name;
    }
}

