<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\GoogleFeed\Model\SearchCondition\Expression;

use XLite\Model\SearchCondition\Expression\Base;

class TypeSearchGroup extends Base
{
    const EMPTY_VALUE = '_empty';

    /**
     * Preprosessing DQL parameter value
     *
     * @param mixed $value
     * @return mixed|string
     */
    protected function preprocessValue($value)
    {
        $value = parent::preprocessValue($value);

        if ($value === static::EMPTY_VALUE) {
            $value = '';
        }

        return $value;
    }

    /**
     * Get DQL expression
     *
     * @param string $alias
     * @return \Doctrine\ORM\Query\Expr|string
     */
    public function getExpression($alias)
    {
        if ($this->getValue() === static::EMPTY_VALUE) {
            $nameWithAlias = $this->getFinalAliasName($alias);
            return sprintf('(%1$s IS NULL OR %1$s %2$s :%3$s)', $nameWithAlias, $this->getOperator(), $this->getParameterName());
        }
        return parent::getExpression($alias);
    }

    /**
     * Get parameter name custom suffix, to avoid collision
     *
     * @return string
     */
    protected function getDefaultParameterNameSuffix()
    {
        return '_search_group_value';
    }

    /**
     * Get DQL expr operator
     *
     * @return string
     */
    protected function getOperator()
    {
        return '=';
    }
}