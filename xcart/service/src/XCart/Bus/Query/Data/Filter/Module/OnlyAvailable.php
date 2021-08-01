<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilter;

class OnlyAvailable extends AFilter
{
    /**
     * @var string|null
     */
    private $coreEdition;

    /**
     * @var string
     */
    private $coreVersion;

    /**
     * @param Iterator    $iterator
     * @param string      $field
     * @param mixed       $data
     * @param string      $coreEdition
     * @param string      $coreVersion
     */
    public function __construct(
        Iterator $iterator,
        $field,
        $data,
        $coreEdition,
        $coreVersion
    ) {
        parent::__construct($iterator, $field, $data);

        $this->coreEdition = $coreEdition;
        $this->coreVersion = $coreVersion;
    }

    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        return $this->data
            ? $this->isAvailable($item)
            : true;
    }

    /**
     * @param Module $module
     *
     * @return bool
     */
    private function isAvailable(Module $module): bool
    {
        if ($module->xcnPlan === -1) {
            return false;
        }

        if ($module['version'] &&
            $this->getMajorVersion($module['installedVersion']) !== $this->coreVersion &&
            $this->getMajorVersion($module['version']) !== $this->coreVersion) {
            return false;
        }

        if ($this->coreEdition && $module->editions && $module->editionState === 2) {
            $editions = array_map(function ($item) {
                return preg_replace('/^\d*_(.+)/', '\\1', $item);
            }, $module->editions);

            return (bool) array_intersect($editions, [$this->coreEdition, 'Free']);
        }

        return true;
    }

    /**
     * @param $version
     *
     * @return string
     */
    private function getMajorVersion($version): string
    {
        [$system, $major, ,] = Module::explodeVersion($version);

        return $system . '.' . $major;
    }
}
