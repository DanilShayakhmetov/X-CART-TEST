<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\ImageResize\Step;

use XLite\Model\Base\Image;

/**
 * Abstract export step
 */
abstract class AStep extends \XLite\Logic\ARepoStep
{
    /**
     * Finalize 
     * 
     * @return void
     */
    public function finalize()
    {
        $this->getRepository()->unmarkAsProcessed();
    }

    // {{{ SeekableIterator, Countable

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $this->countCache = $this->getRepository()->countForResize();
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Row processing

    /**
     * Process model
     *
     * @param \XLite\Model\Base\Image $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $this->correctSize($model);
        $model->prepareSizes(true);
    }

    /**
     * @param Image $storage
     */
    protected function correctSize(Image $storage)
    {
        if (
            $this->isProcessImageStorageCheck($storage)
            && is_array($data = @getimagesize($storage->getStoragePath()))
            && !$this->checkSize($data, $storage)
        ) {
            $storage->setWidth((int)$data[0]);
            $storage->setHeight((int)$data[1]);
        }
    }

    /**
     * @param Image $storage
     *
     * @return bool
     */
    protected function isProcessImageStorageCheck(Image $storage)
    {
        return $storage->isFileExists(null, true);
    }

    /**
     * @param array $data
     * @param Image $storage
     *
     * @return bool
     */
    protected function checkSize(array $data, Image $storage)
    {
        return (int)$data[0] === (int)$storage->getWidth()
            && (int)$data[1] === (int)$storage->getHeight();
    }

    // }}}

    // {{{ Data

    /**
     * Get items iterator
     *
     * @param boolean $reset Reset iterator OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getResizeIterator($this->position, $this->generator->getOptions()->allItems ?? null);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}
}
