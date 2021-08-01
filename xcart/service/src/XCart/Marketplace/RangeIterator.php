<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace;

class RangeIterator implements \Iterator
{
    private $position = 0;

    private $total;

    private $step;

    private $closure;

    public function __construct($closure, $state = null)
    {
        $this->step    = 512 * 1024;
        $this->closure = $closure;

        if ($state) {
            if (isset($state['position'])) {
                $this->position = $state['position'];
            }

            if (isset($state['step'])) {
                $this->step = $state['step'];
            }

            if (isset($state['total'])) {
                $this->total = $state['total'];
            }
        }

        if (!$this->total) {
            $this->initialize();
        }
    }

    public static function getClosure($getDataCallback, $requestName, $params)
    {
        return function ($from, $to) use ($getDataCallback, $requestName, $params) {
            $params['headers']['range'] = "bytes={$from}-{$to}";

            return $getDataCallback($requestName, $params);
        };
    }

    public function getState()
    {
        return [
            'position' => $this->position,
            'total'    => $this->total,
            'step'     => $this->step,
        ];
    }

    public function current()
    {
        return $this->valid() ? $this->doRequest() : null;
    }

    public function next()
    {
        $this->position += $this->step + 1;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return $this->position < $this->total - 1;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function seek($position)
    {
        $this->position = $position;
    }

    protected function initialize()
    {
        $closure  = $this->closure;
        $response = $closure(0, 1);

        if (empty($response['total'])) {
            throw new MarketplaceException('Invalid response');
        }

        $this->total = $response['total'];
    }

    protected function doRequest()
    {
        $closure  = $this->closure;
        $response = $closure(
            $this->position,
            min($this->position + $this->step, $this->total - 1)
        );

        return is_array($response) ? $response['body'] : $response;
    }
}