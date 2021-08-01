<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Client\CloudSearchProvider;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilter;

class Search extends AFilter
{
    /**
     * @var CloudSearchProvider
     */
    protected $cloudSearchProvider;

    /**
     * @param Iterator            $iterator
     * @param string              $field
     * @param mixed               $data
     * @param CloudSearchProvider $cloudSearchProvider
     */
    public function __construct(
        Iterator $iterator,
        $field,
        $data,
        CloudSearchProvider $cloudSearchProvider
    ) {
        parent::__construct($iterator, $field, $data);

        $this->cloudSearchProvider = $cloudSearchProvider;
    }

    /**
     * @return bool
     */
    public function accept()
    {
        if (empty($this->data)) {
            return true;
        }

        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        $accept = stripos($item->authorName, $this->data) !== false
            || stripos($item->moduleName, $this->data) !== false
            || stripos($item->description, $this->data) !== false;

        if (!$accept) {
            $ids = $this->cloudSearchProvider->search($this->data);
            if ($ids) {
                $accept = in_array($item->id, $ids, true);
            }
        }

        return $accept;
    }
}
