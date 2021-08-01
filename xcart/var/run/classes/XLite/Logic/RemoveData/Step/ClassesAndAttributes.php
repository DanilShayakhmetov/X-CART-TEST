<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\RemoveData\Step;


use XLite\Core\Exception\NotImplemented;
use XLite\Core\IteratorsIterator;

class ClassesAndAttributes extends AStep
{
    protected $steps;

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
                $count = array_reduce($this->getSteps(), function ($carry, $item) {
                    /* @var AStep $item */
                    return $carry + $item->count();
                }, 0);

                $options['count' . get_class($this)] = $count;
            }
            $this->countCache = $options['count' . get_class($this)];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Row processing

    /**
     * Process model
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        if ($model instanceof \XLite\Model\ProductClass) {
            $em = \XLite\Core\Database::getEM();
            $identityMap = $em->getUnitOfWork()->getIdentityMap();

            if (isset($identityMap['XLite\Model\Product'])) {
                foreach ($identityMap['XLite\Model\Product'] as $product) {
                    if ($product->getProductClass()
                        && $product->getProductClass()->getId() == $model->getId()
                    ) {
                        $product->setProductClass(null, false);
                    }
                }
            }
        }

        $model->getRepository()->delete($model, false);
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
            $iterator = new IteratorsIterator(array_map(function ($step) {
                return $step->getItems(true);
            }, $this->getSteps()));

            $this->items = $iterator;
        }

        return $this->items;
    }

    // }}}

    protected function getRepository()
    {
        throw new NotImplemented();
    }

    protected function getSteps()
    {
        if (is_null($this->steps)) {
            $this->steps = [
                new GlobalAttributes($this->generator),
                new ProductClasses($this->generator),
            ];
        }

        return $this->steps;
    }
}