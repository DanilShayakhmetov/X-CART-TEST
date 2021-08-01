<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\ApplyAttributeValues\Step;

use XLite\Core\Database;

/**
 * Products
 */
class Products extends \XLite\Logic\ARepoStep
{
    /**
     * Process model
     *
     * @param \XLite\Model\Product $model Product
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $diff = $this->generator->getAttrsDiff();

        foreach ($diff as $attributeId => $changes) {
            $attribute = Database::getRepo('XLite\Model\Attribute')->find($attributeId);
            $attribute->applyChanges($model, $changes);
        }
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $this->countCache = $this->getRepository()->countForApplyAttributeValues();
        }

        return $this->countCache;
    }

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
            $this->items = $this->getRepository()->getApplyAttributeValuesIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }
}
