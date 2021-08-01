<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\Page;

use XLite\Console\Command\SourceCodeGenerators;

class Template
{
    /**
     * @var SourceCodeGenerators\Template
     */
    private $generator;

    public function __construct(SourceCodeGenerators\Template $generator)
    {
        $this->generator = $generator;
        $this->generator->setBodyContentPlaceholder('Hello world!');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function generate($name)
    {
        return $this->generator->generate($name);
    }

}
