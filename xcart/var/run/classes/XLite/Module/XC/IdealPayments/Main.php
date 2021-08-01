<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\IdealPayments;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Add record to the module log file
     *
     * @param string $message Text message OPTIONAL
     * @param mixed  $data    Data (can be any type) OPTIONAL
     *
     * @return void
     */
    public static function addLog($message = null, $data = null)
    {
        if ($message && $data) {
            $msg = array(
                'message' => $message,
                'data'    => $data,
            );

        } else {
            $msg = ($message ?: ($data ?: null));
        }

        if (!is_string($msg)) {
            $msg = var_export($msg, true);
        }

        \XLite\Logger::logCustom(
            'IdealPayments',
            $msg
        );
    }

    /**
     * Get path of SDK classes file
     *
     * @return string
     */
    public static function getLibClassesFile()
    {
        return LC_DIR_MODULES . 'XC' . LC_DS . 'IdealPayments' . LC_DS . 'lib' . LC_DS . 'IdealProRequest.php';
    }
}
