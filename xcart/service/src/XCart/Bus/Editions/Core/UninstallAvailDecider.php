<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Editions\Core;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * Class UninstallAvailDecider
 *
 * @Service\Service()
 */
class UninstallAvailDecider
{
    /**
     * @var array
     */
    protected $editionsModules;

    /**
     * @var EditionStorage
     */
    private $editionStorage;

    /**
     * @param EditionStorage       $editionStorage
     */
    public function __construct(
        EditionStorage $editionStorage
    ) {
        $this->editionStorage = $editionStorage;
    }

    /**
     * @param string $moduleId
     *
     * @return boolean
     */
    public function canBeRemoved($moduleId)
    {
        if (!$this->editionsModules) {
            $this->editionsModules = $this->buildEditionsModules();
        }

        return !isset($this->editionsModules[$moduleId]);
    }

    /**
     * @return array
     */
    protected function buildEditionsModules()
    {
        $editionsModules = [];
        $editions = $this->editionStorage->getEditions();

        foreach ($editions as $edition) {
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $editionsModules = array_merge($editionsModules, $edition['modules']);
        }

        return $editionsModules;
    }
}
