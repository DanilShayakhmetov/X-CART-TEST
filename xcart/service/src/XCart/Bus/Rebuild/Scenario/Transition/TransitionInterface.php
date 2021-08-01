<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\Transition;

use XCart\Bus\Rebuild\Scenario\TransitionInfo;

interface TransitionInterface
{
    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function canOverwrite(TransitionInterface $transition): bool;

    /**
     * @return string
     */
    public function getModuleId(): string;

    /**
     * @return string|null
     */
    public function getVersion(): ?string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param array $state
     *
     * @return array
     */
    public function getStateBeforeTransition(array $state): array;

    /**
     * @param array $state
     *
     * @return array
     */
    public function getStateAfterTransition(array $state): array;

    /**
     * @return TransitionInfo
     */
    public function getInfo(): TransitionInfo;

    /**
     * @param TransitionInfo $info
     */
    public function setInfo(TransitionInfo $info): void;
}
