<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition\Expression;

/**
 * TypeLike
 */
class TypeBetween extends Base
{
    protected function preprocessValue($value)
    {
        sort($value);

        return $value;
    }

    /**
     * Get DQL expression
     *
     * @param  string $alias Root alias
     *
     * @return \Doctrine\ORM\Query\Expr|string
     */
    public function getExpression($alias)
    {
        $nameWithAlias = $this->getFinalAliasName($alias);
        $params = array_keys($this->getParameters());

        return sprintf('%s %s :%s AND :%s', $nameWithAlias, $this->getOperator(), $params[0], $params[1]);
    }

    /**
     * Get parameters list with names and values
     *
     * @return array Keys are parameters names, values are parameters values
     */
    public function getParameters()
    {
        $values = $this->preprocessValue($this->getValue());

        return array(
            $this->getParameterName() . '_start' => $values[0],
            $this->getParameterName() . '_end' => $values[1],
        );
    }

    protected function getDefaultParameterNameSuffix()
    {
        return '_between_value';
    }

    protected function getOperator()
    {
        return 'BETWEEN';
    }
}
