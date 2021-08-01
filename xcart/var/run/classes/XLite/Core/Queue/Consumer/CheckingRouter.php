<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Queue\Consumer;


use Bernard\Envelope;
use Bernard\Exception\ReceiverNotFoundException;
use Bernard\Router\SimpleRouter;

class CheckingRouter extends SimpleRouter
{
    /**
     * {@inheritdoc}
     */
    public function mapChecker(Envelope $envelope)
    {
        $selfChecking = $this->getSelfChecking($envelope->getName());

        if ($selfChecking) {
            return $selfChecking;
        }

        $receiver = $this->get($envelope->getName() . 'Checker');

        if (false == $receiver) {
            throw new CheckingReceiverNotFoundException(sprintf('No checker receiver found for name "%s".', $envelope->getName()));
        }

        if (is_callable($receiver)) {
            return $receiver;
        }

        return array($receiver, lcfirst($envelope->getName()));
    }

    protected function getSelfChecking($name)
    {
        $receiver = $this->get($name);
        if (false == $receiver) {
            throw new ReceiverNotFoundException(sprintf('No receiver found with name "%s".', $name));
        }

        if (is_object($receiver) && method_exists($receiver, lcfirst($name . 'Check'))) {
            return array($receiver, lcfirst($name . 'Check'));
        }

        return null;
    }

}
