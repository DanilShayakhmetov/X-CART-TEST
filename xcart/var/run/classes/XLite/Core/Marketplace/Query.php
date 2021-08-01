<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace;

class Query
{
    private $query;

    private $params;

    private $variables;

    public function __construct($query, $params, $variables = [])
    {
        $this->query     = $query;
        $this->params    = $params;
        $this->variables = $variables;
    }

    public function getQuery()
    {
        return $this->prepareQuery($this->query, $this->params);
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function __toString()
    {
        return $this->getQuery();
    }

    /**
     * @param string $query
     * @param array  $params
     *
     * @return string
     */
    protected function prepareQuery($query, $params)
    {
        if ($params && !empty($params)) {
            return str_replace('%PARAMS%', '(' . implode(',', array_map(function ($k, $v) {
                    return "{$k}: {$this->prepareParam($v)}";
                }, array_keys($params), $params)) . ')', $query);
        }

        return str_replace('%PARAMS%', '', $query);
    }

    protected function prepareParam($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return '"' . str_replace('"', '\"', $value) . '"';
        }

        if (is_array($value)) {
            return '[' . implode(',', array_map([$this, 'prepareParam'], $value)) . ']';
        }

        return (string) $value;
    }
}