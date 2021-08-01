<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

interface IDataSource
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param array $data
     *
     * @return bool
     */
    public function saveAll(array $data): bool;

    /**
     * @return bool
     */
    public function clear(): bool;

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function find($id);

    /**
     * @param mixed $value
     * @param mixed $id
     *
     * @return bool
     */
    public function saveOne($value, $id = null): bool;

    /**
     * @param mixed $id
     *
     * @return bool
     */
    public function removeOne($id): bool;
}
