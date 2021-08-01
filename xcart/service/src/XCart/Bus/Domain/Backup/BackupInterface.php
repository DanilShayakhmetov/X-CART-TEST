<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Backup;

interface BackupInterface
{
    /**
     * @param string $scriptId
     *
     * @return self
     */
    public function create($scriptId);

    /**
     * @param string $scriptId
     *
     * @return self
     */
    public function load($scriptId);

    /**
     * @param string|\Iterator $file
     */
    public function addReplaceRecord($file);

    /**
     * @param string $file
     */
    public function addCreateRecord($file);

    /**
     * @return string[]
     */
    public function getCreated();

    /**
     * @return array
     */
    public function getContentList();
}
