<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use Exception;
use XCart\Bus\Query\Context;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Query\Data\ScenarioDataSource;
use XCart\Bus\Query\Data\ScriptStateDataSource;
use XCart\Bus\Query\Data\ScriptStepStateDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\ScriptStepState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\Bus\Rebuild\Upgrade\UpgradeEntry;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ServiceRebuildResolver
{
    /**
     * @var RebuildResolver
     */
    protected $rebuildResolver;

    /**
     * @var ScenarioDataSource
     */
    protected $scenarioDataSource;

    /**
     * @var ChangeUnitProcessor
     */
    protected $changeUnitProcessor;

    /**
     * @var ScriptStateDataSource
     */
    protected $scriptStateDataSource;

    /**
     * @var ScriptStepStateDataSource
     */
    protected $scriptStepStateDataSource;

    protected $upgradeResolver;

    protected $modulesResolver;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var ResolveInfo
     */
    private $resolveInfo;

    /**
     * @var array
     */
    private $modules;

    /**
     * @var array
     */
    protected static $errorActions = [
        'file-modification-dialog' => StepInterface::ACTION_RELEASE,
        'postponed-hooks-dialog'   => StepInterface::ACTION_RELEASE,
        'reload-page'              => StepInterface::ACTION_RELEASE,
        'note-pre_upgrade'         => StepInterface::ACTION_RELEASE,
        'note-post_upgrade'        => StepInterface::ACTION_RELEASE,
    ];

    public function __construct(
        RebuildResolver $rebuildResolver,
        ScenarioDataSource $scenarioDataSource,
        ChangeUnitProcessor $changeUnitProcessor,
        ScriptStateDataSource $scriptStateDataSource,
        ScriptStepStateDataSource $scriptStepStateDataSource,
        UpgradeResolver $upgradeResolver,
        ModulesResolver $modulesResolver
    ) {
        $this->rebuildResolver = $rebuildResolver;
        $this->upgradeResolver = $upgradeResolver;
        $this->modulesResolver = $modulesResolver;

        $this->changeUnitProcessor = $changeUnitProcessor;

        $this->scenarioDataSource        = $scenarioDataSource;
        $this->scriptStateDataSource     = $scriptStateDataSource;
        $this->scriptStepStateDataSource = $scriptStepStateDataSource;
    }

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return ScriptState
     * @throws Exception
     *
     * @Resolver()
     */
    public function rebuild($value, $args, Context $context, ResolveInfo $info): ?ScriptState
    {
        $this->setVariables($context, $info);

        $scenario = $this->createScenario();

        return $this->startRebuild($scenario['id']);
    }

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return ScriptState
     * @throws Exception
     *
     * @Resolver()
     */
    public function upgrade($value, $args, Context $context, ResolveInfo $info): ?ScriptState
    {
        $this->setVariables($context, $info);

        $type           = $args['type'] ?? null;
        $upgradeModules = $args['modules'] ?? [];

        if (!$type) {
            throw new Exception('Upgrade type not selected');
        }

        $availableUpgradeTypes = array_map(static function ($item) {
            return $item['name'];
        }, $this->getAvailableUpgradeTypes());

        if ($type !== 'self' && in_array('self', $availableUpgradeTypes, true)) {
            throw new Exception('Upgrade In-App Marketplace (self) first');
        }

        if (!in_array($type, $availableUpgradeTypes, true)) {
            throw new Exception("No upgrade for type {$type}", 1);
        }

        $changeUnits    = [];
        $upgradeEntries = $this->getUpgradeList($type);

        foreach ($upgradeEntries as $entry) {
            if (empty($upgradeModules) || in_array($entry->id, $upgradeModules, true)) {
                if ($entry->canUpgrade) {
                    $changeUnits[] = $this->getUpgradeChangeUnit($entry->id, $entry->entry->version);
                } else {
                    $changeUnits[] = $this->getRemoveChangeUnit($entry->id);
                }
            }
        }

        return $this->startRebuildWithChangeUnits($changeUnits, [
            'type'   => $type === 'self' ? 'self-upgrade' : 'redeploy',
            'reason' => 'upgrade',
        ]);
    }

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return ScriptState|null
     * @throws Exception
     *
     * @Resolver()
     */
    public function setModulesState($value, $args, Context $context, ResolveInfo $info): ?ScriptState
    {
        $this->setVariables($context, $info);

        $changeUnits = [];

        foreach ($args['enable'] ?? [] as $moduleId) {
            $changeUnits[] = $this->getEnableChangeUnit($moduleId);
        }

        foreach ($args['disable'] ?? [] as $moduleId) {
            $changeUnits[] = $this->getDisableChangeUnit($moduleId);
        }

        foreach ($args['install'] ?? [] as $moduleId) {
            $changeUnits[] = $this->getInstallChangeUnit($moduleId);
        }

        foreach ($args['remove'] ?? [] as $moduleId) {
            $changeUnits[] = $this->getRemoveChangeUnit($moduleId);
        }

        return $this->startRebuildWithChangeUnits($changeUnits);
    }

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return ScriptState|null
     * @throws Exception
     *
     * @Resolver()
     */
    public function executeRebuild($value, $args, Context $context, ResolveInfo $info): ?ScriptState
    {
        $state = $this->getStateById($args['id']);

        $this->checkAnotherRunningProcess($state);

        $stepState = new ScriptStepState($state->id, $state->progressValue);
        $stepState->start();

        $this->scriptStepStateDataSource->saveOne($stepState, $state->id);

        if ($args['resetConnection']) {
            $this->finishRequest();
        }

        $state = $this->rebuildResolver->executeRebuild($value, $args, $context, $info);

        if ($state->errorType && isset(static::$errorActions[$state->errorType])) {
            $args['action'] = static::$errorActions[$state->errorType];

            $state = $this->rebuildResolver->executeRebuild($value, $args, $context, $info);
        }

        if ($state->errorTitle) {
            $stepState->error($state->errorTitle);
        } else {
            $stepState->done();
        }

        $this->scriptStepStateDataSource->saveOne($stepState, $state->id);

        return $state;
    }

    /**
     * @param             $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return bool
     * @throws Exception
     *
     * @Resolver()
     */
    public function dropRebuild($value, $args, Context $context, ResolveInfo $info): bool
    {
        return $this->rebuildResolver->dropRebuild($value, $args, $context, $info);
    }

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return ScriptState|null
     * @throws Exception
     *
     * @Resolver()
     */
    public function getCurrentStateInfo($value, $args, Context $context, ResolveInfo $info): ?ScriptState
    {
        return $this->getStateById($args['id']);
    }

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return ScriptStepState|null
     * @throws Exception
     *
     * @Resolver()
     */
    public function getStepStateInfo($value, $args, Context $context, ResolveInfo $info): ?ScriptStepState
    {
        $step = $this->scriptStepStateDataSource->find($args['id']);

        if (!$step) {
            throw new Exception("Couldn't find step");
        }

        return $step;
    }

    /**
     * @param array $changeUnits
     * @param array $options
     *
     * @return ScriptState|null
     * @throws Exception
     */
    protected function startRebuildWithChangeUnits(array $changeUnits, array $options = []): ?ScriptState
    {
        $scenario = $this->createScenario($changeUnits);

        if (empty($scenario['modulesTransitions'])) {
            throw new Exception('Empty transitions list');
        }

        return $this->startRebuild($scenario['id'], $options);
    }

    /**
     * @param array $changeUnits
     *
     * @return array
     * @throws ScenarioRuleException
     */
    protected function createScenario(array $changeUnits = []): array
    {
        $emptyScenario = $this->scenarioDataSource->startEmptyScenario();
        $scenario      = $this->changeUnitProcessor->process($emptyScenario, $changeUnits);

        $this->scenarioDataSource->saveOne($scenario);

        return $scenario;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getAvailableUpgradeTypes(): array
    {
        return $this->upgradeResolver->getAvailableUpgradeTypes(null, [], $this->context, $this->resolveInfo);
    }

    /**
     * @param string $type
     *
     * @return UpgradeEntry[]
     * @throws Exception
     */
    protected function getUpgradeList(string $type): array
    {
        return $this->upgradeResolver->resolveList(null, ['type' => $type], $this->context, $this->resolveInfo);

    }

    /**
     * @param array $filter
     *
     * @return array
     */
    protected function getModulesList($filter = [])
    {
        $modulesData = $this->modulesResolver->resolvePage(null, $filter, $this->context, $this->resolveInfo);

        return iterator_to_array($modulesData['modules']);
    }

    /**
     * @param string $scenarioId
     * @param array  $options
     *
     * @return ScriptState|null
     * @throws Exception
     */
    protected function startRebuild(string $scenarioId, array $options = [])
    {
        $this->scriptStepStateDataSource->clear();

        return $this->rebuildResolver->startRebuild(
            null,
            array_merge($options, ['id' => $scenarioId]),
            $this->context,
            $this->resolveInfo
        );
    }

    /**
     * @param string $id
     *
     * @return ScriptState
     * @throws Exception
     */
    private function getStateById($id): ScriptState
    {
        $this->scenarioDataSource->setCurrentScenarioId($id);
        $state = $this->scriptStateDataSource->find($id);
        if (!$state) {
            throw new Exception("Couldn't find state");
        }

        return $state;
    }

    /**
     * @param Context     $context
     * @param ResolveInfo $info
     */
    protected function setVariables(Context $context, ResolveInfo $info)
    {
        $this->context     = $context;
        $this->resolveInfo = $info;
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getEnableChangeUnit($moduleId): array
    {
        return [
            'id'     => $moduleId,
            'enable' => true,
        ];
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getDisableChangeUnit($moduleId): array
    {
        return [
            'id'     => $moduleId,
            'enable' => false,
        ];
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getInstallChangeUnit($moduleId): array
    {
        $module = $this->getModule($moduleId);

        return [
            'id'      => $moduleId,
            'install' => true,
            'enable'  => true,
            'version' => $module['version'] ?? '',
        ];
    }

    /**
     * @param string $moduleId
     * @param string $version
     *
     * @return array
     */
    protected function getUpgradeChangeUnit($moduleId, $version): array
    {
        return [
            'id'      => $moduleId,
            'upgrade' => true,
            'version' => $version,
        ];
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    protected function getRemoveChangeUnit($moduleId): array
    {
        return [
            'id'     => $moduleId,
            'remove' => true,
        ];
    }

    /**
     * @param string $moduleId
     *
     * @return array|null
     */
    protected function getModule(string $moduleId)
    {
        if ($this->modules === null) {
            $this->modules = $this->getModulesList();
        }

        return $this->modules[$moduleId] ?? null;
    }

    /**
     * @param ScriptState $state
     *
     * @throws Exception
     */
    protected function checkAnotherRunningProcess(ScriptState $state)
    {
        /** @var ScriptStepState $step */
        $step = $this->scriptStepStateDataSource->find($state->id);

        if ($step && $step->isRunning()) {
            throw new Exception('Another process already running');
        }
    }

    /**
     * Finish request and continue execution
     */
    protected function finishRequest()
    {
        ignore_user_abort(true);
        session_write_close();

        if (!fastcgi_finish_request()) {
            ob_end_clean();
            header("Connection: close");
            header("Content-Encoding: none");
            header("Content-Length: 0");

            flush();
        }
    }
}