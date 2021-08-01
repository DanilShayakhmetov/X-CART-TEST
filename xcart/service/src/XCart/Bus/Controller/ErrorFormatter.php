<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Controller;

use GraphQL\Error\FormattedError;
use XCart\Bus\Exception\ScenarioTransitionFailed;

class ErrorFormatter extends FormattedError
{
    /**
     * @param \Throwable $e
     * @param bool|int   $debug
     * @param string     $internalErrorMessage
     *
     * @return array
     * @throws \Throwable
     */
    public static function createFromException($e, $debug = false, $internalErrorMessage = null)
    {
        $formattedError = parent::createFromException($e, $debug, $internalErrorMessage);

        $previous = $e->getPrevious();
        if ($previous instanceof ScenarioTransitionFailed) {
            $formattedError['touchedId'] = $previous->getIdTouched();
            $formattedError['failedId']  = $previous->getIdFailed();
        }

        return $formattedError;
    }
}
