<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

use XLite\Model\SearchCondition\IExpressionProvider;
use XLite\Model\SearchCondition\IRepositoryHandlerCarrier;

/**
 * SearchCaseProcessor
 */
class SearchCaseProcessor implements \XLite\View\ItemsList\ISearchCaseProvider
{
    /**
     * Search params
     *
     * @var [IExpressionProvider]|[IRepositoryHandlerCarrier]
     */
    protected $searchParams;

    /**
     * Search values provider
     *
     * @var ISearchValuesStorage
     */
    protected $searchValuesStorage;

    /**
     * @param array                $searchParams        Search params list
     * @param ISearchValuesStorage $searchValuesStorage Session cell name
     */
    public function __construct(array $searchParams, ISearchValuesStorage $searchValuesStorage)
    {
        $this->searchParams = array_filter(
            $searchParams,
            function ($condition) {
                return isset($condition['condition']);
            }
        );

        $this->searchValuesStorage = $searchValuesStorage;
    }

    /**
     * Preprocess search conditions
     * Set values and process deferred conditions
     */
    protected function preprocessSearchConditions()
    {
        // Fill values for solid search conditions
        foreach ($this->searchParams as $name => $condition) {
            $paramValue = $this->getSearchConditionValue($name);

            if (!is_callable($condition['condition'])) {
                $condition['condition']->setValue($paramValue);
            }
        }

        // Fill values for deferred search conditions.
        // We do it separately because we need searchConditions prepopulated here
        foreach ($this->searchParams as $name => $condition) {
            if (is_callable($condition['condition'])) {
                $searchConditionCallback = $condition['condition'];
                $searchConditionObject   = $searchConditionCallback($this->searchParams);

                $paramValue = $this->getSearchConditionValue($name);

                if ($searchConditionObject) {
                    $searchConditionObject->setValue($paramValue);
                    // Replace callable with real condition
                    $this->searchParams[$name]['condition'] = $searchConditionObject;
                }
            }
        }
    }

    /**
     * Get search case
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchCase()
    {
        $cell = new \XLite\Core\CommonCell();

        $this->preprocessSearchConditions();

        $searchConditions = array_filter(
            $this->searchParams,
            function ($condition) {
                return isset($condition['condition'])
                && is_object($condition['condition'])
                && $condition['condition']->getValue();
            }
        );

        foreach ($searchConditions as $name => $condition) {
            if (is_object($condition['condition'])
                && $condition['condition'] instanceof IExpressionProvider
            ) {
                $cell->{$condition['condition']->getName()} = $condition['condition'];

            } elseif (is_object($condition['condition'])
                && $condition['condition'] instanceof IRepositoryHandlerCarrier
            ) {
                $cell->{$condition['condition']->getName()} = $this->getSearchConditionValue($name);

            } else {
                $cell->{$name} = $this->getSearchConditionValue($name);
            }
        }

        return $cell;
    }

    /**
     * Get param value
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getSearchConditionValue($name)
    {
        return $this->searchValuesStorage->getValue($name);
    }

    /**
     * @param \XLite\View\ItemsList\ISearchValuesStorage $defaultStorage
     */
    public function setDefaultValuesStorage(ISearchValuesStorage $defaultStorage)
    {
        $storage = $this->searchValuesStorage;

        if (
            $storage
            && $storage instanceof ASearchValuesStorage
        ) {
            $storage->passFallbackStorage($defaultStorage);
        }
    }
}
