<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization\Deserializer;

abstract class SchemaGroup
{
    /**
     * @var int
     */
    protected $index;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var SchemaGroup
     */
    protected $parent;

    /**
     * @var SchemaGroup
     */
    protected $root;

    /**
     * @var array
     */
    protected $props = [];

    /**
     * @var string;
     */
    private $paramName;

    /**
     * SchemaGroup constructor.
     *
     * @param int              $count
     * @param SchemaGroup|null $parent
     */
    public function __construct(int $count = 0, SchemaGroup $parent = null)
    {
        $this->root   = $parent ? $parent->getRoot() : $this;
        $this->count  = $count;
        $this->parent = $parent;
        $this->index  = $this->root->getIndex();
    }

    /**
     * @param mixed $prop
     *
     * @return bool
     */
    public function addProp($prop): bool
    {
        if (empty($prop)) {
            return false;
        }

        $value = is_string($prop) ? $this->parseProp($prop) : $prop;

        if ($this->paramName === null) {
            $this->paramName = $value;
            $this->root->increaseIndex();
        } else {
            $this->props[$this->paramName] = $value;

            $this->paramName = null;
        }

        return true;

    }

    /**
     * @return SchemaGroup
     */
    public function getParent(): SchemaGroup
    {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     *
     */
    public function increaseIndex(): void
    {
        $this->index++;
    }

    /**
     * @return SchemaGroup
     */
    public function getRoot(): SchemaGroup
    {
        return $this->root;
    }

    /**
     * @param string $prop
     *
     * @return bool|false|float|int|string|SchemaReference|null
     */
    protected function parseProp(string $prop)
    {
        $params = explode(':', $prop, 3);
        $type   = $params[0];
        $value  = $params[1] ?? null;

        switch ($type) {
            case 'i':
                return (int) $value;

            case 's':
                return substr($params[2], 1, -1);

            case 'b':
                return (bool) $value;

            case 'd':
                return (float) $value;

            case 'r':
                return new SchemaReference($value);

            case 'N':
            default:
                return null;
        }
    }
}