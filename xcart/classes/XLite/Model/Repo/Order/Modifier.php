<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Order;

/**
 * Order modifier repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Order\Modifier", summary="Add new order modifier")
 * @Api\Operation\Read(modelClass="XLite\Model\Order\Modifier", summary="Retrieve order modifier by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Order\Modifier", summary="Retrieve all order modifiers")
 * @Api\Operation\Update(modelClass="XLite\Model\Order\Modifier", summary="Update order modifier by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Order\Modifier", summary="Delete order modifier by id")
 */
class Modifier extends \XLite\Model\Repo\ARepo
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'weight';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('class'),
    );

    /**
     * Find all active modifiers
     *
     * @return array
     */
    public function findActive()
    {
        $list = $this->retrieveModifiers();

        $list = is_array($list) ? new \XLite\DataSet\Collection\OrderModifier($list) : null;

        if ($list) {
            foreach ($list as $i => $item) {
                if (!\XLite\Core\Operator::isClassExists($item->getClass())) {
                    unset($list[$i]);
                }
            }
        }

        return $list;
    }

    /**
     * Retrieve modifiers from database
     *
     * @return \XLite\Model\Order\Modifier[]
     */
    protected function retrieveModifiers()
    {
        return $this->defineFindActiveQuery()->getResult();
    }

    /**
     * Define query for findActive() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindActiveQuery()
    {
        return $this->createQueryBuilder();
    }
}
