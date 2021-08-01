<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('LC_ERR_TAG_MSG',   '@MSG@');
define('LC_ERR_TAG_ERROR', '@ERROR@');
define('LC_ERR_TAG_CODE',  '@CODE@');
define('LC_CURRENT_YEAR', '@CURRENT_YEAR@');
define('LC_CURRENT_VERSION', '@VERSION@');

define('LC_ERROR_PAGE_MESSAGE', 'ERROR: "' . LC_ERR_TAG_ERROR . '" (' . LC_ERR_TAG_CODE . ') - ' . LC_ERR_TAG_MSG . LC_EOL);

/**
 * Display error message
 *
 * @param string  $code    Error code
 * @param string  $message Error message
 * @param string  $page    Template of message to display
 *
 * @return void
 */
function showErrorPage($code, $message, $page = LC_ERROR_PAGE_MESSAGE, $prefix = 'ERROR_', $http_code = 500)
{
    header('Content-Type: text/html; charset=utf-8', true, $http_code);

    $page = str_replace(
        [
            LC_ERR_TAG_MSG,
            LC_ERR_TAG_ERROR,
            LC_ERR_TAG_CODE,
            LC_CURRENT_YEAR,
            LC_CURRENT_VERSION
        ],
        [
            $message,
            str_replace($prefix, '', $code),
            defined($code) ? constant($code) : 'N/A',
            date('Y'),
            LC_VERSION
        ],
        $page
    );

    if (PHP_SAPI === 'cli') {
        echo $page;
    } else {
        echo '<script>document.addEventListener("DOMContentLoaded", function(event) {document.body.innerHTML=' . json_encode($page) . ';});</script>';
    }

    exit (intval($code) ? $code : 1);
}

// Check PHP version before any other operations
if (!defined('LC_DO_NOT_CHECK_PHP_VERSION') && PHP_VERSION_ID < 70209) {
    showErrorPage('ERROR_UNSUPPORTED_PHP_VERSION', 'Min allowed PHP version is 7.2.9');
}
