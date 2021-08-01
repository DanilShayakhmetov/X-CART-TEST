<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

/**
 * Request
 */
class ThemeTweaker extends \XLite\Base\Singleton
{
    const MODE_LAYOUT_EDITOR = 'layout_editor';
    const MODE_LABELS_EDITOR = 'labels_editor';
    const MODE_WEBMASTER = 'webmaster';
    const MODE_CUSTOM_CSS = 'custom_css';
    const MODE_INLINE_EDITOR = 'inline_editor';

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInLayoutMode()
    {
        return $this->isInMode(self::MODE_LAYOUT_EDITOR)
            && $this->canRunThemeTweaker()
            && method_exists(\XLite::getController(), 'isCheckoutLayout')
            && !\XLite::getController()->isCheckoutLayout()
            && !\XLite::isAdminZone();
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInWebmasterMode()
    {
        $conditions = $this->getDefaultRunConditions();
        unset($conditions['not_post'], $conditions['not_ajax']);

        $editorAllowed = \XLite::isAdminZone()
            ? static::isAdminTargetAllowedInWebmasterMode()
            : static::isTargetAllowedInWebmasterMode() && $this->isInMode(self::MODE_WEBMASTER);

        return $this->canRunThemeTweaker($conditions)
            && $editorAllowed;
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isTargetAllowedInWebmasterMode()
    {
        return 'image' !== \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isAdminTargetAllowedInWebmasterMode()
    {
        return 'notification_editor' === \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Check if inline editor mode is available
     *
     * @return boolean
     */
    public function isInInlineEditorMode()
    {
        return $this->isInMode(self::MODE_INLINE_EDITOR)
            && static::isTargetAllowedInInlineEditorMode()
            && $this->canRunThemeTweaker()
            && !\XLite::isAdminZone();
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isTargetAllowedInInlineEditorMode()
    {
        $targets = ['product', 'category', 'page', 'main'];

        return in_array(\XLite\Core\Request::getInstance()->target, $targets, true);
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInLabelsMode()
    {
        return $this->isInMode(self::MODE_LABELS_EDITOR)
            && $this->canRunThemeTweaker()
            && !\XLite::isAdminZone();
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInCustomCssMode()
    {
        return $this->isInMode(self::MODE_CUSTOM_CSS)
            && $this->canRunThemeTweaker()
            && !\XLite::isAdminZone();
    }

    /**
     * Get current mode
     * @return string
     */
    public function getCurrentMode()
    {
        return \XLite\Core\Session::getInstance()->themetweaker_mode;
    }

    /**
     * Set current mode
     */
    public function setCurrentMode($mode)
    {
        if (null === $mode || in_array($mode, $this->getAvailableModes(), true)) {
            \XLite\Core\Session::getInstance()->themetweaker_mode = $mode;
            \XLite\Core\Session::getInstance()->themetweaker_cache_key = uniqid();
        }
    }

    /**
     * Get available modes
     * @return string
     */
    public function getAvailableModes()
    {
        return [
            self::MODE_LAYOUT_EDITOR,
            self::MODE_LABELS_EDITOR,
            self::MODE_WEBMASTER,
            self::MODE_CUSTOM_CSS,
            self::MODE_INLINE_EDITOR
        ];
    }

    /**
     * Check if is in the specific mode
     * @return boolean
     */
    public function isInMode($mode)
    {
        return $this->getCurrentMode() === $mode;
    }

    /**
     * Checks if themetweaker mode can be run
     * @param array $conditions Array of callables to check for (should return false if the mode cannot be run)
     * @return bool
     */
    public function canRunThemeTweaker($conditions = null)
    {
        if ($conditions === null) {
            $conditions = $this->getDefaultRunConditions();
        }

        return !in_array(false, $conditions, true);
    }

    /**
     * @return array
     */
    protected function getDefaultRunConditions()
    {
        return [
            'not_post' => !\XLite\Core\Request::getInstance()->isPost(),
            'not_cli'  => !\XLite\Core\Request::getInstance()->isCLI(),
            'not_ajax' => !\XLite\Core\Request::getInstance()->isAJAX(),
            'user_allowed' => static::isUserAllowed(),
            'not_rebuilding' => !\Includes\Decorator\Utils\CacheManager::isRebuildNeeded()
        ];
    }

    /**
     * Check user allowed
     *
     * @return boolean
     */
    public static function isUserAllowed()
    {
        $auth = \XLite\Core\Auth::getInstance();

        return $auth->getProfile()
            && $auth->getProfile()->isAdmin()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Checks checkbox config value and casts to proper boolean
     *
     * @param $value
     * @return bool
     */
    public static function castCheckboxValue($value)
    {
        return true === $value || 'true' === $value || '1' === $value || 'Y' === $value;
    }
}
