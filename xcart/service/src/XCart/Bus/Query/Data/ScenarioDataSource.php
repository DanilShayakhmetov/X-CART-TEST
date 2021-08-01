<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ScenarioDataSource extends SerializedDataSource
{
    /**
     * @var string|null
     */
    private $currentScenarioId;

    /**
     * @param Application      $app
     * @param StorageInterface $storage
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        StorageInterface $storage
    ) {
        return new static(
            $storage->build($app['config']['cache_dir'], 'scenarioStorage')
        );
    }

    /**
     * @param string $scenarioId
     *
     * @return array
     */
    public function startScenario($scenarioId): array
    {
        $this->setCurrentScenarioId($scenarioId);

        return $this->find($scenarioId) ?? [];
    }

    /**
     * @param string      $type
     * @param string|null $returnUrl
     *
     * @return array
     */
    public function startEmptyScenario($type = 'common', $returnUrl = null): array
    {
        $scenarioId = uniqid('scenario', true);

        $this->setCurrentScenarioId($scenarioId);

        return [
            'id'                 => $scenarioId,
            'date'               => time(),
            'updatedAt'          => 0,
            'type'               => $type,
            'modulesTransitions' => [],
            'changeUnits'        => [],
            'returnUrl'          => $returnUrl,
        ];
    }

    /**
     * @return string|null
     */
    public function getCurrentScenarioId(): ?string
    {
        return $this->currentScenarioId;
    }

    /**
     * @param string|null $currentScenarioId
     */
    public function setCurrentScenarioId(?string $currentScenarioId): void
    {
        $this->currentScenarioId = $currentScenarioId;
    }
}
