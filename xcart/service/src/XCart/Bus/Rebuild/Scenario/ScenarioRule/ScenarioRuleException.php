<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule;

use Throwable;

class ScenarioRuleException extends \Exception
{
    public const HARD_EXCEPTION = 0;
    public const SOFT_EXCEPTION = 1;

    /**
     * @var array
     */
    private $params;

    /**
     * @param string         $message
     * @param array          $params
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', array $params = [], int $code = self::HARD_EXCEPTION, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->params = $params;
    }

    /**
     * @param string $moduleId
     * @param string $dependencyId
     *
     * @return ScenarioRuleException
     */
    public static function fromDependenciesCoreVersionCoreUpgradeRequired($moduleId, $dependencyId): ScenarioRuleException
    {
        return new self(
            'scenario-rule.exception.dependencies.core-version.core-upgrade-required',
            [$moduleId, $dependencyId]
        );
    }

    /**
     * @param string $moduleId
     * @param string $dependencyId
     *
     * @return ScenarioRuleException
     */
    public static function fromDependenciesCoreVersionModuleUpgradeRequired($moduleId, $dependencyId): ScenarioRuleException
    {
        return new self(
            'scenario-rule.exception.dependencies.core-version.module-upgrade-required',
            [$moduleId, $dependencyId],
            self::SOFT_EXCEPTION
        );
    }

    /**
     * @param string $moduleId
     * @param string $dependencyId
     *
     * @return ScenarioRuleException
     */
    public static function fromDependenciesCoreVersionNonExistentModule($moduleId, $dependencyId): ScenarioRuleException
    {
        return new self(
            'scenario-rule.exception.dependencies.core-version.non-existent-module',
            [$moduleId, $dependencyId],
            self::SOFT_EXCEPTION
        );
    }

    /**
     * @param string $moduleId
     * @param string $incompatibleId
     *
     * @return ScenarioRuleException
     */
    public static function fromDependenciesForceDisabledWhenIncompatible($moduleId, $incompatibleId): ScenarioRuleException
    {
        return new self(
            'scenario-rule.exception.dependencies.force-disabled-when-incompatible',
            [$moduleId, $incompatibleId],
            self::SOFT_EXCEPTION
        );
    }

    /**
     * @param string $moduleId
     * @param string $requiredById
     *
     * @return ScenarioRuleException
     */
    public static function fromDependenciesForceEnabledWhenRequired($moduleId, $requiredById): ScenarioRuleException
    {
        return new self(
            'scenario-rule.exception.dependencies.force-enabled-when-required',
            [$moduleId, $requiredById],
            self::SOFT_EXCEPTION
        );
    }

    /**
     * @param string $moduleId
     *
     * @return ScenarioRuleException
     */
    public static function fromCoreVersionCoreUpgradeRequired($moduleId): ScenarioRuleException
    {
        return new self(
            'scenario-rule.exception.core-version.core-upgrade-required',
            [$moduleId]
        );
    }

    /**
     * @param string $moduleId
     *
     * @return ScenarioRuleException
     */
    public static function fromCoreVersionModuleUpgradeRequired($moduleId): ScenarioRuleException
    {
        return new self(
            'scenario-rule.exception.core-version.module-upgrade-required',
            [$moduleId],
            self::SOFT_EXCEPTION
        );
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
