<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Auth;

/**
 * Auth by keys
 */
class Keys
{
    protected $keyFull;
    protected $keyRead;

    public function __construct($keyFull, $keyRead)
    {
        $this->keyFull  = $keyFull;
        $this->keyRead  = $keyRead;
    }

    /**
     * Is key allowed to read/write
     *
     * @param  string $key
     * @return boolean
     */
    public function allowFull($key)
    {
        return $this->keyFull && $this->keyFull == $key;
    }

    /**
     * Is key allowed to write
     *
     * @param  string $key
     * @return boolean
     */
    public function allowWrite($key)
    {
        return $this->allowFull($key);
    }

    /**
     * Is key allowed to read
     *
     * @param  string $key
     * @return boolean
     */
    public function allowRead($key)
    {
        return $this->allowFull($key)
            || ($this->keyRead && $this->keyRead == $key);
    }
}
