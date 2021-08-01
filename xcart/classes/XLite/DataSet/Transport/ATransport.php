<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\DataSet\Transport;

/**
 * Abstract transport
 */
abstract class ATransport extends \XLite\Base implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Data storage
     *
     * @var array
     */
    protected $data = [];

    /**
     * Storage allowed keys list (cache)
     *
     * @var array
     */
    protected $keys;

    /**
     * Define keys
     *
     * @return array
     */
    abstract protected function defineKeys();

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Map data
     *
     * @param array $data Data
     *
     * @return void
     */
    public function map(array $data)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Clear
     *
     * @return void
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * Check transport complexity
     *
     * @return boolean
     */
    public function check()
    {
        $result = true;

        foreach ($this->getKeys() as $k) {
            if (!isset($this->$k)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Get keys list
     *
     * @return array
     */
    protected function getKeys()
    {
        if (null === $this->keys) {
            $this->keys = $this->defineKeys();
        }

        return $this->keys;
    }

    // {{{ Magic methods

    /**
     * Getter
     *
     * @param string $name Storage cell name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return in_array($name, $this->getKeys(), true) && isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Setter
     *
     * @param string $name  Cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->getKeys(), true)) {
            $this->data[$name] = $value;
        }
    }

    /**
     * Check - cell is set or not
     *
     * @param string $name Cell name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return in_array($name, $this->getKeys(), true) && isset($this->data[$name]);
    }

    /**
     * Unset cell
     *
     * @param string $name Cell name
     *
     * @return void
     */
    public function __unset($name)
    {
        if (in_array($name, $this->getKeys(), true) && isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['data'];
    }

    public function __wakeup()
    {
    }

    // }}}

    // {{{ Countable

    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        return count($this->getKeys());
    }

    // }}}

    // {{{ IteratorAggregate

    /**
     * Get iterator
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        $list = [];
        foreach ($this->getKeys() as $k) {
            $list[$k] = isset($this->$k) ? $this->$k : null;
        }

        return new \ArrayIterator($list);
    }

    // }}}

    // {{{ ArrayAccess

    /**
     * Check - is offset exists or not
     *
     * @param mixed $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get offset value
     *
     * @param mixed $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set offset value
     *
     * @param mixed $offset Offset
     * @param mixed $value  Value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset offset
     *
     * @param mixed $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    // }}}
}
