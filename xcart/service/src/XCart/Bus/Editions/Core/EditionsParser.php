<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Editions\Core;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EditionsParser
 */
class EditionsParser
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $expectedEditions;

    /**
     * EditionsParser constructor.
     *
     * @param       $path
     * @param array $expectedEditions
     */
    public function __construct($path, $expectedEditions)
    {
        $this->path             = $path;
        $this->expectedEditions = $expectedEditions;
    }

    /**
     * @param string $lng
     *
     * @return array
     */
    public function getEditions($lng): array
    {
        $rawData = null;

        if (file_exists($this->path)) {
            try {
                $fileContent = file_get_contents($this->path);
                $rawData     = Yaml::parse($fileContent);
            } catch (ParseException $exception) {
                $rawData = [];
            }
        }

        $editions = $rawData['Distrs'] ?? [];

        $result = [];

        foreach ($this->expectedEditions as $key => $editionName) {
            $data     = $this->getEditionData($editionName, $lng, $editions);
            $combined = $this->mapData($data);

            $nextEditions = array_slice($this->expectedEditions, $key + 1);
            foreach ($nextEditions as $nextEditionName) {
                $nextEdition = $this->getEditionData($nextEditionName, $lng, $editions, false);

                $shouldBeDisabled = $this->mapData(array_map(function () {
                        return false;
                    }, $nextEdition)
                );

                /** @noinspection SlowArrayOperationsInLoopInspection */
                $combined = array_merge($combined, $shouldBeDisabled);
            }

            $result[$editionName] = $combined;
        }

        return $result;
    }

    /**
     * @param string $name
     * @param string $lng
     *
     * @return array
     */
    public function getEdition($name, $lng): array
    {
        $rawData = null;

        if (file_exists($this->path)) {
            try {
                $fileContent = file_get_contents($this->path);
                $rawData     = Yaml::parse($fileContent);
            } catch (ParseException $exception) {
                $rawData = [];
            }
        }

        $editions = $rawData['Distrs'] ?? [];

        $data = $this->getEditionData($name, $lng, $editions, false);

        return $this->mapData($data);
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function mapData($data): array
    {
        $names = array_map(function ($name) {
            return str_replace('/', '-', $name);
        }, array_keys($data));

        return array_combine(
            $names,
            $this->mapModules(array_values($data))
        );
    }

    /**
     * @param       $name
     * @param       $lng
     * @param array $distrs
     * @param bool  $include
     *
     * @return array
     */
    protected function getEditionData($name, $lng, $distrs, $include = true): array
    {
        if (!isset($distrs[$name][$lng])) {
            return [];
        }

        $distrData = $distrs[$name];

        $include           = $include && isset($distrData[$lng]['include'])
            ? $distrData[$lng]['include']
            : false;
        $commonEditionData = $distrData['all']['modules'] ?? [];

        $onlyEditionData = $distrData[$lng]['modules'] ?? [];

        $editionData = array_merge(
            $commonEditionData,
            $onlyEditionData
        );

        if ($include) {
            $editionData = array_merge(
                $this->getEditionData($include[0], $include[1], $distrs),
                $editionData
            );
        }

        return $editionData;
    }

    /**
     * @param $modules
     *
     * @return array
     */
    protected function mapModules($modules)
    {
        return array_map(
            function ($state) {
                return $state ? 'E' : 'D';
            },
            $modules
        );
    }
}
