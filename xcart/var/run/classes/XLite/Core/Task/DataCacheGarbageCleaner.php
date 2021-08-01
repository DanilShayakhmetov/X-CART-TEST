<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task;


class DataCacheGarbageCleaner extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Step max execution time(in seconds)
     */
    const MAX_EXECUTION_TIME = self::INT_1_MIN;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return static::t('Removing expired datacache');
    }

    /**
     * @inheritdoc
     */
    protected function runStep()
    {
        $endTime = time() + static::MAX_EXECUTION_TIME;

        if (file_exists(LC_DIR_DATACACHE)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(LC_DIR_DATACACHE),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            /** @var \SplFileInfo $file */
            while ($endTime > time() && $iterator->valid() && ($file = $iterator->current())) {
                if ($file->isFile()) {
                    $handle = fopen($file->getRealPath(), 'rb');
                    $line = fgets($handle);
                    $lifetime = $line !== false ? (int)$line : false;
                    fclose($handle);

                    if ($lifetime !== 0 && $lifetime < time()) {
                        unlink($file->getRealPath());
                    }
                }

                $iterator->next();
            }
        }
    }

    /**
     * @inheritdoc
     */
    protected function getPeriod()
    {
        return static::INT_10_MIN;
    }
}