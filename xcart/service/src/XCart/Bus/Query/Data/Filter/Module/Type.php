<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilter;

/**
 * @DataSourceFilter(name="type")
 */
class Type extends AFilter
{
    public const TAG_TEMPLATES = 'Templates';
    public const TYPE_ADDON    = 'addon';
    public const TYPE_SKIN     = 'skin';
    public const TYPE_CUSTOM   = 'custom';

    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        if (!$this->data) {
            return true;
        }

        if ($this->data === static::TYPE_ADDON) {
            return !$this->isTemplate($item);
        }

        if ($this->data === static::TYPE_SKIN) {
            return $this->isTemplate($item);
        }

        if ($this->data === static::TYPE_CUSTOM) {
            return $item['private'] || $item['custom'];
        }

        if (isset($item->type)) {
            return $item->type === $this->data;
        }

        return false;
    }

    /**
     * @param Module $item
     *
     * @return bool
     */
    private function isTemplate($item): bool
    {
        if (isset($item->type)) {
            return $item->type === static::TYPE_SKIN;
        }

        if (isset($item->tags)) {
            return in_array(static::TAG_TEMPLATES, array_map(static function ($item) { return $item['id']; }, $item->tags), true);
        }

        return false;
    }
}
