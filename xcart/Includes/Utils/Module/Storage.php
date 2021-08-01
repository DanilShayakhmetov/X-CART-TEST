<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

use Includes\Utils\FileManager;

class Storage implements IStorage
{
    const STORAGE_FILE_NAME = '.modules.php';

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function fetch()
    {
        return $this->unserialize(FileManager::read($this->getFilePath(self::STORAGE_FILE_NAME)));
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data)
    {
        return FileManager::write($this->getFilePath(self::STORAGE_FILE_NAME), $this->serialize($data));
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getFilePath($fileName)
    {
        return LC_DIR_VAR . $fileName;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function serialize(array $data)
    {
        return @serialize($data);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    private function unserialize($data)
    {
        return @unserialize($data) ?: [];
    }
}
