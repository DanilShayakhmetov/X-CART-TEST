<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\RemoveData\Step;


class GlobalAttributes extends \XLite\Logic\RemoveData\Step\AStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\Attribute
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Attribute');
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $options = $this->generator->getOptions();
            if (!isset($options['count' . get_class($this)])) {
                $options['count' . get_class($this)] = $this->getRepository()->countForRemoveGlobalAttributesData();
            }
            $this->countCache = $options['count' . get_class($this)];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Data

    /**
     * Get items iterator
     *
     * @param boolean $reset Reset iterator OPTIONAL
     *
     * @return \Iterator
     */
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getRemoveGlobalAttributesDataIterator($this->position);
        }

        return $this->items;
    }

    // }}}
}