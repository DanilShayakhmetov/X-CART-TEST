<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates\Plugin;

/**
 * APlugin
 *
 * @package XLite
 */
abstract class APlugin extends \Includes\Decorator\Plugin\Templates\ATemplates
{
    protected static $versionKey;

    public function __construct()
    {
        if (!$this->getVersionKey()) {
            $this->generateVersionKey();
        }

        \XLite\Model\ViewList::setVersionKey($this->getVersionKey());
    }

    /**
     * @return string
     */
    public function getVersionKeyFileName()
    {
        return LC_DIR_VAR . '.ViewListsVersionKey';
    }

    /**
     * @return string
     */
    public function getVersionKey()
    {
        if (!static::$versionKey) {
            static::$versionKey = \Includes\Utils\FileManager::read(
                $this->getVersionKeyFileName()
            );
        }

        return static::$versionKey;
    }

    /**
     * @return bool
     */
    public function generateVersionKey()
    {
        return \Includes\Utils\FileManager::write(
            $this->getVersionKeyFileName(),
            md5(microtime() . mt_rand(1, 1000))
        );
    }

    /**
     * @return bool
     */
    public function deleteVersionKey()
    {
        return \Includes\Utils\FileManager::deleteFile(
            $this->getVersionKeyFileName()
        );
    }

    /**
     * Check - current plugin is bocking or not
     *
     * @return boolean
     */
    public function isBlockingPlugin()
    {
        return !$this->getVersionKey();
    }

    /**
     * Omits any key, specified in $keys, from $node array and returns new array with omitted key-value pairs
     *
     * @param array $node
     * @param array $keys
     *
     * @return array($node, $omitted)
     */
    protected function omitKeys(array $node, array $keys)
    {
        $omitted = [];

        foreach ($keys as $key) {
            if (isset($node[$key])) {
                $omitted[$key] = $node[$key];
                unset($node[$key]);
            }
        }

        return [$node, $omitted];
    }
}
