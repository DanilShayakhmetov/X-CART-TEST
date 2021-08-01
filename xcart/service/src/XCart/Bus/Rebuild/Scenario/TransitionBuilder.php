<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario;

use Exception;
use Psr\Log\LoggerInterface;
use XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule\ChangeUnitBuildRuleInterface;
use XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule\ConflictResolver;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;

class TransitionBuilder
{
    /**
     * @var ConflictResolver
     */
    private $conflictResolver;

    /**
     * @var ChangeUnitBuildRuleInterface[]
     */
    private $rules;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param array            $rules
     * @param ConflictResolver $conflictResolver
     * @param LoggerInterface  $logger
     */
    public function __construct(
        array $rules,
        ConflictResolver $conflictResolver,
        LoggerInterface $logger
    ) {
        $this->rules = array_combine(
            array_map(static function ($rule) {
                /** @var ChangeUnitBuildRuleInterface $rule */
                return $rule->getName();
            }, $rules),
            $rules
        );

        $this->conflictResolver = $conflictResolver;
        $this->logger           = $logger;
    }

    /**
     * @param array $changeUnit
     *
     * @return TransitionInterface|null
     * @throws Exception
     */
    public function build(array $changeUnit): ?TransitionInterface
    {
        $results = [];
        /** @var ChangeUnitBuildRuleInterface $rule */
        foreach ($this->rules as $name => $rule) {
            if ($rule->isApplicable($changeUnit)) {
                $results[$name] = $rule->build($changeUnit);
            }
        }

        if (count($results) > 1) {
            $results = $this->conflictResolver->resolve($this->rules, $results);

            if (count($results) !== 1) {
                $this->logger->critical(
                    'Can\'t resolve conflicts in transitions',
                    [
                        'changeUnit' => $changeUnit,
                        'results'    => $results,
                    ]
                );

                throw new Exception("Can't resolve conflicts in transitions");
            }
        }

        return $results
            ? reset($results)
            : null;
    }
}
