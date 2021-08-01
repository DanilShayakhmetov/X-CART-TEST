<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor;

use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Rebuild\Executor\Script\ScriptInterface;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ScriptFactory
{
    /**
     * @var string[]|ScriptInterface[]
     */
    private $scripts = [];

    /**
     * @var array
     */
    private $steps = [];

    /**
     * @var Application
     */
    private $app;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Application     $app
     * @param LoggerInterface $logger
     */
    public function __construct(
        Application $app,
        LoggerInterface $logger
    ) {
        $this->app    = $app;
        $this->logger = $logger;
    }

    /**
     * @param string $name
     * @param string $script
     */
    public function addScript($name, $script): void
    {
        $this->scripts[$name] = $script;
    }

    /**
     * @param string $scriptName
     * @param string $step
     * @param int    $weight
     */
    public function addStep($scriptName, $step, $weight): void
    {
        $this->steps[$scriptName][] = [
            'weight' => $weight,
            'step'   => $step,
        ];
    }

    /**
     * @param string $name
     *
     * @return ScriptInterface|null
     */
    public function createScript($name): ?ScriptInterface
    {
        $script = $this->getScript($name);
        if ($script) {
            $script->setSteps($this->getScriptSteps($name));

            return $script;
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return ScriptInterface|null
     */
    private function getScript($name): ?ScriptInterface
    {
        if (isset($this->scripts[$name])) {
            $script = $this->scripts[$name];

            return $this->app[$script] ?? new $script;
        }

        $this->logger->error(sprintf('Script for type "%s" is missing', $name));

        return null;
    }

    /**
     * @param string $name
     *
     * @return StepInterface[]
     */
    private function getScriptSteps($name): array
    {
        $steps = $this->steps[$name] ?? [];

        usort($steps, static function ($a, $b) {
            $a = (int) $a['weight'];
            $b = (int) $b['weight'];

            if ($a === $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        });

        return array_map(function ($item) {
            $step = $item['step'];

            return $this->app[$step] ?? new $step;
        }, $steps);
    }
}
