<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;


class IteratorsIterator implements \Iterator
{
    /**
     * @var \Iterator[]
     */
    private $iterators;
    /**
     * @var \Iterator|null
     */
    private $active;

    public function __construct(array $iterators)
    {
        $this->iterators = array_values($iterators);
        $this->rewind();
    }

    public function current()
    {
        return $this->active
            ? $this->active->current()
            : null;
    }

    public function next()
    {
        if ($this->active) {
            $this->active->next();

            if (!$this->active->valid()) {
                $this->pushQueue();
            }
        }
    }

    public function key()
    {
        return $this->active
            ? $this->active->key()
            : null;
    }

    public function valid()
    {
        if ($this->active) {
            if (!$this->active->valid()) {
                $this->pushQueue();
                return $this->valid();
            }

            return true;
        }

        return false;
    }

    public function rewind()
    {
        $this->active = isset($this->iterators[0]) ? $this->iterators[0] : null;

        array_map(function ($i) {
            $i->rewind();
        }, $this->iterators);
    }

    private function pushQueue()
    {
        if (
            $this->active
        ) {
            $this->active = isset($this->iterators[array_search($this->active, $this->iterators, true) + 1])
                ? $this->iterators[array_search($this->active, $this->iterators, true) + 1]
                : null;
        }
    }
}