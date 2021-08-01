<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\Transition;

use XCart\Bus\Rebuild\Scenario\TransitionInfo;

abstract class TransitionAbstract implements TransitionInterface
{
    /**
     * @var string
     */
    private $moduleId;

    /**
     * @var string|null
     */
    private $version;

    /**
     * @var TransitionInfo
     */
    private $info;

    /**
     * @param string      $moduleId
     * @param string|null $version
     */
    public function __construct($moduleId, $version = null)
    {
        $this->moduleId = $moduleId;
        $this->version  = $version;
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function canOverwrite(TransitionInterface $transition): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param array $state
     *
     * @return array
     */
    public function getStateBeforeTransition(array $state): array
    {
        return $state;
    }

    /**
     * @return TransitionInfo
     */
    public function getInfo(): TransitionInfo
    {
        return $this->info ?: new TransitionInfo();
    }

    /**
     * @param TransitionInfo $info
     */
    public function setInfo(TransitionInfo $info): void
    {
        $this->info = $info;
    }
}
