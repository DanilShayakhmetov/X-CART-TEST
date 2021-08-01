<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types;

use Pimple\Exception\UnknownIdentifierException;
use Silex\Application;

trait ObjectTypeTrait
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     *
     * @throws UnknownIdentifierException
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        parent::__construct($this->prepareConfig($this->defineConfig()));
    }

    /**
     * @return array
     */
    abstract protected function defineConfig();

    /**
     * @param array $config
     *
     * @return array
     * @throws UnknownIdentifierException
     */
    protected function prepareConfig($config)
    {
        return $config;
    }
}
