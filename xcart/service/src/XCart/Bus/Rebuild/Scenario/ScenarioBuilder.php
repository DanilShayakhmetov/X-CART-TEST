<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario;

use GraphQL\Type\Definition\ResolveInfo;
use Silex\Application;
use Psr\Log\LoggerInterface;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Query\Resolver\LanguageDataResolver;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleInterface;
use XCart\Bus\Rebuild\Scenario\Transition\EnableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\DisableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\RemoveTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class ScenarioBuilder
{
    /**
     * @var ScenarioRuleInterface[]
     */
    private $rules;

    /**
     * @var TransitionInterface[]
     */
    private $moduleTransitions = [];

    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $languageMessages;

    /**
     * @param Application                  $app
     * @param InstalledModulesDataSource   $installedModulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param CoreConfigDataSource         $coreConfigDataSource
     * @param LoggerInterface              $logger
     * @param LanguageDataResolver         $languageDataResolver
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        InstalledModulesDataSource $installedModulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        LoggerInterface $logger,
        LanguageDataResolver $languageDataResolver
    ) {
        // todo: use annotations
        return new self(
            [
                $app[ScenarioRule\Dependencies\InstallNotInstalled::class],
                $app[ScenarioRule\Dependencies\UpgradeDependency::class],
                $app[ScenarioRule\Dependencies\CoreVersion::class],
                $app[ScenarioRule\Dependencies\EnableNotEnabled::class],
                $app[ScenarioRule\Dependencies\ForceEnabledWhenRequired::class],
                $app[ScenarioRule\Dependencies\ForceDisabledWhenIncompatible::class],
                $app[ScenarioRule\Dependencies\ForceSystemDisabledIfIncompatible::class],
                $app[ScenarioRule\Dependencies\DisableNonRelatedSkins::class],
                $app[ScenarioRule\CoreVersion::class],
            ],
            $installedModulesDataSource,
            $logger,
            $languageDataResolver
        );
    }

    /**
     * @param ScenarioRuleInterface[]    $rules
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param LoggerInterface            $logger
     * @param LanguageDataResolver       $languageDataResolver
     */
    public function __construct(
        array $rules,
        InstalledModulesDataSource $installedModulesDataSource,
        LoggerInterface $logger,
        LanguageDataResolver $languageDataResolver
    ) {
        $this->rules                      = $rules;
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->logger                     = $logger;

        $messages = $languageDataResolver->getLanguageMessages(null, [], null, new ResolveInfo([]));
        $this->languageMessages = [];
        foreach ((array) $messages as $label) {
            $this->languageMessages[$label['name']] = $label['label'];
        }
    }

    /**
     * @param TransitionInterface $transition
     *
     * @throws ScenarioRuleException
     */
    public function addTransition(TransitionInterface $transition): void
    {
        if (isset($this->moduleTransitions[$transition->getModuleId()])
            && !$transition->canOverwrite($this->moduleTransitions[$transition->getModuleId()])
        ) {
            $transition = $this->moduleTransitions[$transition->getModuleId()];
        }

        $rules = $this->getRules();

        foreach ($rules as $rule) {
            if ($rule->isApplicable($transition)) {
                try {
                    $rule->applyFilter($transition, $this);

                } catch (ScenarioRuleException $exception) {
                    if ($exception->getCode() === ScenarioRuleException::SOFT_EXCEPTION) {
                        $message = $exception->getMessage();
                        $this->logger->notice(
                            LanguageDataResolver::getMessageWithReplacedParams(
                                $this->languageMessages[$message] ?? $message,
                                $exception->getParams()
                            )
                        );
                        return;
                    }

                    throw $exception;
                }

                $rule->applyTransform(
                    $transition,
                    $this
                );
            }
        }

        $this->moduleTransitions[$transition->getModuleId()] = $transition;
    }

    /** @noinspection PhpDocRedundantThrowsInspection */

    /**
     * @return TransitionInterface[]
     */
    public function getTransitions(): array
    {
        return $this->moduleTransitions;
    }

    /**
     * @param string $id
     *
     * @return TransitionInterface|null
     */
    public function getTransition($id): ?TransitionInterface
    {
        return $this->moduleTransitions[$id] ?? null;
    }

    /**
     * @param string $id
     *
     * @return boolean
     */
    public function removeTransition($id): bool
    {
        if (isset($this->moduleTransitions[$id])) {
            unset($this->moduleTransitions[$id]);

            return true;
        }

        return false;
    }

    /**
     * @return ScenarioRuleInterface[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param $rules ScenarioRuleInterface[]
     */
    public function setRules($rules): void
    {
        $this->rules = $rules;
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return TransitionInterface
     */
    public function fillSystemTransitionInfo(TransitionInterface $transition): TransitionInterface
    {
        $info = new TransitionInfo();
        $info->setReason('system');

        $transition->setInfo($info);

        return $transition;
    }

    /**
     * Adds system modules transitions
     * @throws ScenarioRuleException
     */
    public function addSystemTransitions(): void
    {
        $transitions = [];

        $modules                = $this->installedModulesDataSource->getAll();
        $installedSystemModules = array_filter($modules, function ($module) {
            /** @var Module $module */
            return !empty($module->isSystem);
        });

        foreach ($installedSystemModules as $systemModuleId => $systemModule) {
            if ($systemModuleId === 'CDev-Core') {
                continue;
            }

            if ($systemModuleId === 'XC-Service') {
                continue;
            }

            if (!isset($this->moduleTransitions[$systemModuleId])) {
                $transition = $GLOBALS['config']->getOption('performance', 'ignore_system_modules')
                    ? new DisableTransition($systemModuleId)
                    : new EnableTransition($systemModuleId);
                $transitions[] = $this->fillSystemTransitionInfo($transition);
            }
        }

        foreach ($transitions as $transition) {
            $this->addTransition($transition);
        }
    }
}
