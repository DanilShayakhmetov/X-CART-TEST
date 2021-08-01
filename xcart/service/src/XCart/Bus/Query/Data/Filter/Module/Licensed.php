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

class Licensed extends AFilter
{
    /**
     * @var string|null
     */
    private $edition;

    /**
     * @param Iterator    $iterator
     * @param string      $field
     * @param mixed       $data
     * @param string|null $edition
     */
    public function __construct(
        Iterator $iterator,
        $field,
        $data,
        $edition
    ) {
        parent::__construct($iterator, $field, $data);

        $this->edition = $edition;
    }

    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        return $this->data ? (bool) $item->license : $this->isInactive($item);
    }

    /**
     * @param Module $module
     *
     * @return bool
     */
    private function isInactive(Module $module): bool
    {
        if (!$module->installed) {
            return false;
        }

        if (((int) $module->price) === 0 && empty($module->editions)) {
            return $module->xcnPlan === -1;
        }

        if ($this->edition && $module->editions && $module->editionState === 2) {
            $editions = array_map(function ($item) {
                return preg_replace('/^\d*_(.+)/', '\\1', $item);
            }, $module->editions);

            if (!array_intersect($editions, [$this->edition, 'Free'])) {
                return true;
            }
        }

        return $module->price > 0 && $module->xbProductId && empty($module->license);
    }
}
