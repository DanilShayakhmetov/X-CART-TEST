<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Sorter;

use ArrayIterator;
use Iterator;
use XCart\Bus\Domain\Module;

class Sorter extends ArrayIterator
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @param Iterator $iterator
     * @param array    $fields
     */
    public function __construct(Iterator $iterator, $fields)
    {
        parent::__construct(iterator_to_array($iterator));

        $this->fields = $this->parseFields($fields);

        $this->uasort(function ($a, $b) {
            /** @var Module $a */
            /** @var Module $b */
            return $this->compareByAllFields($a, $b);
        });
    }

    /**
     * @param Module $a
     * @param Module $b
     *
     * @return int
     */
    private function compareByAllFields($a, $b): int
    {
        if ($a->id === 'XC-Service') {
            return -1;
        }

        if ($b->id === 'XC-Service') {
            return 1;
        }

        if ($a->id === 'CDev-Core') {
            return -1;
        }

        if ($b->id === 'CDev-Core') {
            return 1;
        }

        foreach ($this->fields as [$field, $direction]) {
            $aField = $a->{$field} ?? null;
            $bField = $b->{$field} ?? null;

            if (preg_match('/(\w.*)\((.*)\)/', $direction, $matches)) {
                [, $sorter, $arg] = $matches;

                if ($sorter === 'list') {
                    return $this->compareByList($bField, $aField, explode(',', $arg));
                }
            }

            if ($field === 'version') {
                $comparisionResult = $direction === 'desc'
                    ? version_compare($bField, $aField)
                    : version_compare($aField, $bField);
            } else {
                $comparisionResult = $direction === 'desc'
                    ? $this->compare(strtolower($bField), strtolower($aField))
                    : $this->compare(strtolower($aField), strtolower($bField));
            }

            if ($comparisionResult !== 0) {
                return $comparisionResult;
            }
        }

        return 0;
    }

    /**
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    private function compare($a, $b): int
    {
        /** @noinspection TypeUnsafeComparisonInspection */
        if ($a == $b) {
            return 0;
        }

        return ($a > $b) ? 1 : -1;
    }

    /**
     * @param string $a
     * @param string $b
     * @param array  $array
     *
     * @return int
     */
    private function compareByList($a, $b, array $array): int
    {
        $keys  = array_flip($array);
        $count = count($array);

        return ($keys[strtolower($b)] ?? $count) <=> ($keys[strtolower($a)] ?? $count);
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    private function parseFields($fields): array
    {
        return array_map(static function ($item) {
            $sorter = explode(' ', trim($item));

            return [
                $sorter[0],
                isset($sorter[1]) ? strtolower($sorter[1]) : 'asc',
            ];
        }, $fields);
    }
}
