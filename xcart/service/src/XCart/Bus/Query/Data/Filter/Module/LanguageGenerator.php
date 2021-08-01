<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilterGenerator;
use XCart\Bus\Query\Data\Filter\AModifier;
use XCart\Bus\Query\Data\TagsDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @DataSourceFilter(name="language")
 * @Service\Service()
 */
class LanguageGenerator extends AFilterGenerator
{
    /**
     * @var TagsDataSource
     */
    private $tagsDataSource;

    /**
     * @param TagsDataSource $tagsDataSource
     */
    public function __construct(
        TagsDataSource $tagsDataSource
    ) {
        $this->tagsDataSource = $tagsDataSource;
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return Language
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        return new Language(
            $iterator,
            $field,
            $data,
            $this->tagsDataSource
        );
    }
}
