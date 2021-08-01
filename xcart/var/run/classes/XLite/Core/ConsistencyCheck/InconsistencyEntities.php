<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck;

/**
 * Class Inconsistency
 * @package XLite\Core\ConsistencyCheck
 */
class InconsistencyEntities extends Inconsistency
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * InconsistencyEntities constructor.
     *
     * @param string    $type
     * @param string    $message
     * @param array     $entities
     */
    public function __construct($type, $message, $entities)
    {
        parent::__construct($type, $message);

        $this->entities = $entities;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
