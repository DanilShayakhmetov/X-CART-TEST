<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

use XCart\Bus\Rebuild\Executor\StepState;

class RebuildException extends \Exception
{
    /**
     * @var string
     */
    private $type = 'rebuild-dialog';

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var StepState
     */
    private $stepState;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data ?? [];
    }

    /**
     * @param mixed $data
     *
     * @return static
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $data
     * @param string $index
     *
     * @return static
     */
    public function addData($data, $index = null)
    {
        if ($index !== null) {
            $this->data[$index] = $data;
        } else {
            $this->data[] = $data;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return static
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return StepState
     */
    public function getStepState()
    {
        return $this->stepState;
    }

    /**
     * @param StepState $stepState
     *
     * @return static
     */
    public function setStepState($stepState)
    {
        $this->stepState = $stepState;

        return $this;
    }
}
