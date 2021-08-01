<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter;

use Iterator;
use IteratorIterator;
use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service
 */
class FilterLocator
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var string[]
     */
    private $filters = [];

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function add($name, $class): void
    {
        $this->filters[$name] = $class;
    }

    /**
     * @param string $name
     *
     * @return string|Iterator
     */
    public function get($name)
    {
        return $this->filters[$name] ?? null;
    }

    /**
     * @param Iterator $iterator
     * @param string   $name
     * @param mixed    $data
     *
     * @return IteratorIterator
     */
    public function wrap($iterator, $name, $data): IteratorIterator
    {
        $filter = $this->get($name);

        if (is_a($filter, AFilterGenerator::class, true)) {
            return $this->app[$filter]($iterator, $name, $data);
        }

        return $filter ? new $filter($iterator, $name, $data) : new Simple($iterator, $name, $data);
    }

    /**
     * @param Iterator $iterator
     * @param array    $list
     *
     * @return IteratorIterator
     */
    public function chain($iterator, array $list): IteratorIterator
    {
        foreach ($list as $name => $data) {
            if ($data === null) {
                continue;
            }
            $iterator = $this->wrap($iterator, $name, $data);
        }

        return $iterator;
    }
}
