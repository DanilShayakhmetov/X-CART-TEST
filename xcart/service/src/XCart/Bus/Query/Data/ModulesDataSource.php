<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use ArrayIterator;
use Iterator;
use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\NotImplemented;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\Filter\FilterLocator;
use XCart\Bus\Query\Data\Flatten\Flatten;
use XCart\Bus\Query\Data\Limit\Limit;
use XCart\Bus\Query\Data\Sorter\Sorter;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ModulesDataSource implements IDataSource
{
    /**
     * @var array
     */
    private $runtimeCache;

    /**
     * @var InstalledModulesDataSource
     */
    private $installedModules;

    /**
     * @var MarketplaceModulesDataSource
     */
    private $marketplaceModules;

    /**
     * @var FilterLocator
     */
    private $filterLocator;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var Iterator[]
     */
    private $flattenCache = [];

    /**
     * @var array
     */
    private $flattenArrayCache = [];

    /**
     * @param InstalledModulesDataSource   $installedModules
     * @param MarketplaceModulesDataSource $marketplaceModules
     * @param FilterLocator                $filterLocator
     * @param Context                      $context
     */
    public function __construct(
        InstalledModulesDataSource $installedModules,
        MarketplaceModulesDataSource $marketplaceModules,
        FilterLocator $filterLocator,
        Context $context
    ) {
        $this->installedModules   = $installedModules;
        $this->marketplaceModules = $marketplaceModules;
        $this->filterLocator      = $filterLocator;
        $this->context            = $context;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        if (!$this->runtimeCache) {
            $data = array_map(static function ($item) {
                return [$item];
            }, $this->installedModules->getAll());

            foreach ($this->marketplaceModules->getAll() as $id => $module) {
                $data[$id] = array_merge($data[$id] ?? [], $module);
            }

            $this->runtimeCache = $data;
        }

        return $this->runtimeCache;
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function find($id)
    {
        $data = $this->getAll();

        return $data[$id] ?? null;
    }

    /**
     * @param array $data
     *
     * @return bool
     * @throws NotImplemented
     */
    public function saveAll(array $data): bool
    {
        throw new NotImplemented('Cannot perform saveAll() operation on combined modules data source');
    }

    /**
     * @param mixed       $value
     * @param string|null $id
     *
     * @return bool
     * @throws NotImplemented
     */
    public function saveOne($value, $id = null): bool
    {
        throw new NotImplemented('Cannot perform saveOne() operation on combined modules data source');
    }

    /**
     * @param mixed $id
     *
     * @return bool
     * @throws NotImplemented
     */
    public function removeOne($id): bool
    {
        throw new NotImplemented('Cannot perform removeOne() operation on combined modules data source');
    }

    /**
     * @return bool
     * @throws NotImplemented
     */
    public function clear(): bool
    {
        throw new NotImplemented('Cannot perform clear() operation on combined modules data source');
    }

    /**
     * @param string $id
     * @param string $version
     * @param array  $filters
     * @param array  $replaceData
     *
     * @return Module
     */
    public function findOne($id, $version = Flatten::RULE_LAST, array $filters = [], array $replaceData = [])
    {
        $flattenArray = $this->getFlattenArray($version);

        if (!isset($flattenArray[$id])) {
            return null;
        }

        foreach ($replaceData as $key => $param) {
            $flattenArray[$id][$key] = $param;
        }

        $iterator = $this->filteredIterator(
            new ArrayIterator([$flattenArray[$id]]),
            ['language' => $this->context->languageCode ?: 'en'] + $filters
        );

        $found = iterator_to_array($iterator);

        return array_pop($found);
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param string $version
     * @param array  $filters
     * @param array  $sorters
     * @param array  $limit
     *
     * @return Module[]
     */
    public function getSlice(
        $version = Flatten::RULE_LAST,
        array $filters = [],
        array $sorters = [],
        array $limit = []
    ): array {
        $iterator = $this->sliceIterator(
            $this->sortIterator(
                $this->filteredIterator(
                    $this->getFlatten($version),
                    ['language' => $this->context->languageCode ?: 'en'] + $filters
                ),
                $sorters
            ),
            $limit
        );

        return iterator_to_array($iterator);
    }

    /**
     * @param string $rule
     *
     * @return Iterator
     */
    public function getFlatten($rule = Flatten::RULE_LAST): Iterator
    {
        if (!isset($this->flattenCache[$rule])) {
            $this->flattenCache[$rule] = $this->flattenIterator($this->getAll(), $rule);
        }

        return $this->flattenCache[$rule];
    }

    /**
     * @param string $rule
     *
     * @return Module[]
     */
    public function getFlattenArray($rule = Flatten::RULE_LAST): array
    {
        if (!isset($this->flattenArrayCache[$rule])) {
            $this->flattenArrayCache[$rule] = iterator_to_array($this->getFlatten($rule));
        }

        return $this->flattenArrayCache[$rule];
    }

    /**
     * @param array|Iterator $data
     * @param string         $rule
     *
     * @return Flatten
     */
    public function flattenIterator($data, $rule = Flatten::RULE_LAST): Flatten
    {
        return new Flatten(
            $data instanceof Iterator ? $data : new ArrayIterator($data),
            $this->installedModules->find('CDev-Core'),
            $rule
        );
    }

    /**
     * @param array|Iterator $data
     * @param array          $filters
     *
     * @return Iterator
     */
    public function filteredIterator($data, $filters): Iterator
    {
        return $this->filterLocator->chain(
            $data instanceof Iterator ? $data : new ArrayIterator($data),
            $filters
        );
    }

    /**
     * @param array|Iterator $data
     * @param array          $sorters
     *
     * @return Iterator
     */
    public function sortIterator($data, $sorters): Iterator
    {
        $iterator = $data instanceof Iterator ? $data : new ArrayIterator($data);

        return $sorters ? new Sorter($iterator, $sorters) : $iterator;
    }

    /**
     * @param array|Iterator $data
     * @param array          $limit
     *
     * @return Iterator
     */
    public function sliceIterator($data, $limit): Iterator
    {
        $iterator = $data instanceof Iterator ? $data : new ArrayIterator($data);

        return $limit ? new Limit($iterator, $limit) : $iterator;
    }

    public function clearRuntimeCache()
    {
        $this->runtimeCache      = [];
        $this->flattenCache      = [];
        $this->flattenArrayCache = [];
    }
}
