<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Client\XCart;
use XCart\Bus\Client\XCartCLI;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Module;
use XCart\Bus\Helper\HookFilter;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\AEventHook;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "17000")
 * @RebuildStep(script = "install", weight = "5000")
 */
class RebuildHook extends AEventHook
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @param Application     $app
     * @param HookFilter      $hookFilter
     * @param XCart           $client
     * @param XCartCLI        $cliClient
     * @param LoggerInterface $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        HookFilter $hookFilter,
        XCart $client,
        XCartCLI $cliClient,
        LoggerInterface $logger
    ) {
        return new static(
            $app['config']['root_dir'],
            $hookFilter,
            $app[InstalledModulesDataSource::class],
            $client,
            $cliClient,
            $logger
        );
    }

    /**
     * @param string                     $rootDir
     * @param HookFilter                 $hookFilter
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param XCart                      $client
     * @param XCartCLI                   $cliClient
     * @param LoggerInterface            $logger
     */
    public function __construct(
        $rootDir,
        HookFilter $hookFilter,
        InstalledModulesDataSource $installedModulesDataSource,
        XCart $client,
        XCartCLI $cliClient,
        LoggerInterface $logger
    ) {
        parent::__construct($rootDir, $hookFilter, $client, $cliClient, $logger);

        $this->installedModulesDataSource = $installedModulesDataSource;
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'rebuild';
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return string|null
     */
    protected function getCacheId(ScriptState $scriptState): ?string
    {
        $parentStepState = $scriptState->getCompletedStepState(UpdateModulesList::class);

        return $parentStepState ? $parentStepState->data['cacheId'] : null;
    }

    /**
     * @param array[] $transitions
     *
     * @return array[]
     */
    protected function filterScriptTransitions(array $transitions): array
    {
        return $transitions;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getScriptTransitions(ScriptState $scriptState): array
    {
        $transitions = [];
        foreach ($this->getEnabledModulesList($scriptState->transitions) as $author => $modules) {
            foreach (array_keys($modules) as $name) {
                $id = Module::buildModuleId($author, $name);

                $transitions[$id] = [
                    'id' => $id,
                ];
            }
        }

        return $transitions;
    }

    /**
     * @param $transitions
     *
     * @return array
     */
    private function getEnabledModulesList(array $transitions): array
    {
        $result = [];

        foreach ($transitions as $transition) {
            [$author, $name] = explode('-', $transition['id']);
            if ($transition['stateAfterTransition']['enabled']) {
                if (!isset($result[$author])) {
                    $result[$author] = [];
                }

                $result[$author][$name] = true;
            }
        }

        foreach ($this->installedModulesDataSource->getAll() as $module) {
            /** @var Module $module */
            if (isset($result[$module->author][$module->name])) {
                continue;
            }

            if ($module->enabled) {
                if (!isset($result[$module->author])) {
                    $result[$module->author] = [];
                }

                $result[$module->author][$module->name] = true;
            }
        }

        return $result;
    }
}
