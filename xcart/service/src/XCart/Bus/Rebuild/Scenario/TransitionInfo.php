<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario;

class TransitionInfo
{
    /**
     * @var string|null
     */
    private $reason;

    /**
     * @var string|null
     */
    private $reasonHuman;

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     */
    public function setReason($reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @return string|null
     */
    public function getReasonHuman(): ?string
    {
        return $this->reasonHuman;
    }

    /**
     * @param string|null $reasonHuman
     */
    public function setReasonHuman($reasonHuman): void
    {
        $this->reasonHuman = $reasonHuman;
    }
}
