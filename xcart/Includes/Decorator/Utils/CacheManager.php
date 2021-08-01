<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Utils;

use Includes\Decorator\Plugin\Doctrine\Utils\SchemaMigrationManager;
use Includes\Utils\FileManager;

/**
 * Cache manager
 *
 * TODO to be rewritten after first interation of XCN-8332
 */
abstract class CacheManager extends \Includes\Decorator\Utils\AUtils
{
    /**
     * Available hooks
     */
    const HOOK_BEFORE_CLEANUP  = 'before_cleanup';
    const HOOK_BEFORE_DECORATE = 'before_decorate';
    const HOOK_BEFORE_WRITE    = 'before_write';
    const HOOK_DECORATE        = 'decorate';
    const HOOK_WRITE           = 'write';
    const HOOK_STEP_FIRST      = 'step_first';
    const HOOK_STEP_SECOND     = 'step_second';
    const HOOK_STEP_THIRD      = 'step_third';
    const HOOK_STEP_FOURTH     = 'step_fourth';
    const HOOK_STEP_FIFTH      = 'step_fifth';
    const HOOK_STEP_SIX        = 'step_six';
    const HOOK_STEP_SEVEN      = 'step_seven';
    const HOOK_STEP_EIGHT      = 'step_eight';
    const HOOK_STEP_NINE       = 'step_nine';
    const HOOK_STEP_TEN        = 'step_ten';
    const HOOK_STEP_ELEVEN     = 'step_eleven';
    const HOOK_STEP_TWELVE     = 'step_twelve';
    const HOOK_STEP_THIRTEEN   = 'step_thirteen';

    /**
     * Cache key argument name
     */
    const KEY_NAME = 'cacheId';

    /**
     * Current process ID argument name
     */
    const CPID_NAME = 'cpid';

    /**
     * Cache key name validation pattern
     */
    const KEY_NAME_PATTERN = '/^[a-zA-Z0-9]{3,32}$/Ss';

    /**
     * Current state file key suffix
     */
    const CURRENT_FILE_KEY_SUFFIX = null;

    /**
     * Flag: skip step completion
     *
     * @var boolean
     */
    public static $skipStepCompletion = false;

    /**
     * List of cache building steps
     *
     * @var array
     */
    protected static $steps = [
        self::STEP_FIRST,
        self::STEP_SECOND,
        self::STEP_THIRD,
        self::STEP_FOURTH,
        self::STEP_FIFTH,
        self::STEP_SIX,
        self::STEP_SEVEN,
        // self::STEP_EIGHT,
        self::STEP_NINE,
        self::STEP_TEN,
        self::STEP_ELEVEN,
        self::STEP_TWELVE,
        self::STEP_THIRTEEN,
    ];

    /**
     * Timestamp of the step start
     *
     * @var integer
     */
    protected static $stepStart;

    /**
     * Memory usage
     *
     * @var integer
     */
    protected static $stepMemory;

    /**
     * Current PID
     *
     * @var string
     */
    protected static $currentPid = null;

    /**
     * kCache rebuild process ey
     *
     * @var   string
     */
    protected static $key;

    // {{{ Dispaly message routines

    /**
     * showStepMessage
     *
     * @param string  $text             Message text
     * @param boolean $addNewline       Flag OPTIONAL
     * @param boolean $addJS            Add message in javascript wrapper OPTIONAL
     * @param boolean $logTimestamp     Add step timestamps to the log file OPTIONAL
     *
     * @return void
     */
    public static function showStepMessage($text, $addNewline = false, $addJS = false, $logTimestamp = false)
    {
        static::$stepStart = microtime(true);
        static::$stepMemory = memory_get_usage();

        \Includes\Utils\Operator::showMessage($text, $addNewline, $addJS);

        if (trim($text)) {
            if ($logTimestamp) {
                $text = '[' . date('H:i:s') . ']' . $text;
            }
            static::logMessage($text . ($addNewline ? PHP_EOL . PHP_EOL : ''));
        }
    }

    /**
     * showStepInfo
     *
     * @return void
     */
    public static function showStepInfo()
    {
        $text = number_format(microtime(true) - static::$stepStart, 2) . 'sec, ';

        $memory = memory_get_usage();
        $text .= \Includes\Utils\Converter::formatFileSize($memory, '');
        $text .= ' (' . \Includes\Utils\Converter::formatFileSize(memory_get_usage() - static::$stepMemory, '') . ')';

        \Includes\Utils\Operator::showMessage(' [' . $text . ']', true, false);

        static::logMessage(' ' . $text . PHP_EOL);
    }

    /**
     * Return true if time from step begin is exceeds the specified value
     *
     * @param integer $value
     *
     * @return boolean
     */
    public static function isTimeExceeds($value)
    {
        return microtime(true) - static::$stepStart > $value;
    }

    /**
     * Log message
     *
     * @param string $message Message
     *
     * @return void
     */
    public static function logMessage($message)
    {
        $newLine = substr($message, -1 * strlen(PHP_EOL)) === PHP_EOL;

        $message = preg_replace(
            [
                '/<script[^>]*>.+<\/script>/USs',
            ],
            [
                '',
            ],
            $message
        );

        $message = strip_tags($message);
        $message = preg_replace('/(\s)+/', '\1', trim($message));
        $pathPart = date('Y') . LC_DS . date('m');
        $path = LC_DIR_LOG . $pathPart . LC_DS . 'decorator.log.' . date('Y-m-d') . '.php';
        if (!\Includes\Utils\FileManager::isExists($path)) {
            $message = '<' . '?php die(); ?' . '>' . PHP_EOL . $message;
        }

        \Includes\Utils\FileManager::write($path, $message . ($newLine ? PHP_EOL : ''), FILE_APPEND);
    }

    /**
     * Get decorator message
     *
     * @return string
     */
    protected static function getMessage()
    {
        return 'Executing step ' . static::$step . ' of ' . static::LAST_STEP . '.';
    }

    /**
     * Get plain text notice block
     *
     * @return string
     */
    protected static function getPlainMessage()
    {
        return PHP_EOL . static::getMessage() . PHP_EOL;
    }

    /**
     * getHTMLMessage
     *
     * @return string
     */
    protected static function getHTMLMessage()
    {
        $message = static::getMessage() . ' Please, don\'t close this page until the whole process is finished.' . PHP_EOL;

        return static::getJSMessage()
            . <<<HTML
<div class="rebuild-message">
  $message
</div>
HTML;
    }

    /**
     * Get JS code to prevent user to close page before cache re-building step will complete
     *
     * @return string
     */
    protected static function getJSMessage()
    {
        return !isset($_GET['doNotRedirectAfterCacheIsBuilt'])
            ? <<<OUT
<script type="text/javascript">
<!--
  window.onbeforeunload = confirmExit;
  function confirmExit()
  {
    return 'If you leave this page then the cache will not be re-built and your store will be non-functional.';
  }
-->
</script>
OUT
            : '';
    }

    /**
     * Get JS code to disable onbeforeunload action
     *
     * @return string
     */
    protected static function getJSFinishMessage()
    {
        return <<<OUT
<script language="JavaScript" type="text/javascript">
<!--
  window.onbeforeunload = null;
-->
</script>
OUT;
    }

    /**
     * displayCompleteMessage
     *
     * @return void
     */
    protected static function displayCompleteMessage()
    {
        echo '<div id="finish">Cache is built successfully</div>';
    }

    // }}}

    // {{{ Cache state indicator routines

    /**
     * Set cache rebuild mark
     *
     * @return void
     */
    public static function setCacheRebuildMark()
    {
        static::$key = static::generateKey();

        \Includes\Utils\FileManager::write(
            static::getRebuildMarkFileName(),
            static::$key
        );
    }

    /**
     * Remove cache rebuild mark
     *
     * @return void
     */
    public static function unsetCacheRebuildMark()
    {
        \Includes\Utils\FileManager::deleteFile(
            static::getRebuildMarkFileName()
        );
    }

    /**
     * Getc ache rebuild mark
     *
     * @return string
     */
    public static function getCacheRebuildMark()
    {
        return \Includes\Utils\FileManager::read(
            static::getRebuildMarkFileName()
        );
    }

    /**
     * Check if cache rebuild is in progress (not necessarily by the current process, an other process may rebuild cache in the separate var/run folder).
     *
     * @return bool
     */
    public static function isRebuildInProgress()
    {
        return \Includes\Utils\FileManager::isExists(
            static::getRebuildIndicatorFileName()
        );
    }

    /**
     * Clean up the cache rebuild indicator
     *
     * @return void
     */
    public static function cleanupRebuildIndicator()
    {
        \Includes\Utils\FileManager::deleteFile(static::getRebuildIndicatorFileName());
    }

    /**
     * Clean up the cache validity indicators
     *
     * @return void
     */
    public static function cleanupCacheIndicators()
    {
        foreach (static::getCacheStateFiles() as $file) {
            if (\Includes\Utils\FileManager::isFile($file) && !\Includes\Utils\FileManager::deleteFile($file)) {
                \Includes\ErrorHandler::fireError(
                    'Unable to delete "' . $file . '" file. Please correct the permissions'
                );
            }
        }

        // "Step is running" indicator
        static::cleanupRebuildIndicator();
    }

    /**
     * Check and (if needed) remove the rebuild indicator file
     *
     * @return boolean
     */
    public static function checkRebuildIndicatorState()
    {
        $result = true;

        if (static::isRebuildAllowed()) {
            $name = static::getRebuildIndicatorFileName();
            $content = \Includes\Utils\FileManager::read($name);

            $result = !LC_IS_CLI_MODE && !empty($content) && static::getRebuildIndicatorFileContent() != $content;
        }

        return $result;
    }

    /**
     * Remove rebuild indicator file
     *
     * @return void
     */
    public static function removeRebuildIndicatorFile()
    {
        if (static::isRebuildAllowed()) {
            $name = static::getRebuildIndicatorFileName();
            $content = \Includes\Utils\FileManager::read($name);

            // Only the process created the file can delete
//            if (!empty($content) && (LC_IS_CLI_MODE || static::getRebuildIndicatorFileContent() == $content)) {
            \Includes\Utils\FileManager::deleteFile($name);
//            }
        }
    }

    /**
     * Return true if rebuild cache is allowed
     *
     * @return boolean
     */
    protected static function isRebuildAllowed()
    {
        return defined('XCN_ADMIN_SCRIPT')
            || \Includes\Utils\ConfigParser::getOptions(['performance', 'developer_mode']);
    }

    /**
     * Remove cache validity indicator
     *
     * @param string $step Current step name
     *
     * @return void
     */
    protected static function clear($step)
    {
        $file = static::getCacheStateIndicatorFileName($step);

        if ($file) {
            \Includes\Utils\FileManager::deleteFile($file);
        }
    }

    /**
     * Return name of the file, which indicates the cache state
     *
     * @param int $step Current step name
     *
     * @return string
     */
    protected static function getCacheStateIndicatorFileName($step)
    {
        return static::getCompileDir() . '.cacheGenerated.' . $step . '.step';
    }

    /**
     * Return name of the file, which indicates if the build process neded
     *
     * @return string
     */
    protected static function getRebuildMarkFileName()
    {
        return LC_DIR_VAR . '.rebuildMark';
    }

    /**
     * Return name of the file, which indicates if the build process started
     *
     * @return string
     */
    protected static function getRebuildIndicatorFileName()
    {
        return LC_DIR_VAR . '.rebuildStarted';
    }

    /**
     * Data to write into the "step completed" file indicator
     *
     * @return string
     */
    protected static function getCacheStateIndicatorFileContent()
    {
        return date('r');
    }

    /**
     * Data to write into the "step started" file indicator
     *
     * @return string
     */
    protected static function getRebuildIndicatorFileContent()
    {
        if (!isset(static::$currentPid)) {
            static::$currentPid = !empty($_REQUEST[static::CPID_NAME])
                ? $_REQUEST[static::CPID_NAME]
                : md5(microtime() . mt_rand(1, 1000));
        }

        return static::$currentPid;
    }

    /**
     * Check if cache rebuild process is already started
     *
     * @return boolean
     */
    protected static function checkIfRebuildStarted()
    {
        if (static::isRebuildBlock() && static::checkRebuildIndicatorState()) {
            static::triggerMaintenanceModeError();
        }
    }

    /**
     * Trigger a storefront access error to display the maintenance mode screen
     */
    public static function triggerMaintenanceModeError()
    {
        \Includes\ErrorHandler::fireError(
            \Includes\Utils\ConfigParser::getInstallationLng() === 'ru'
                ? 'Мы вносим новые изменения в наш магазин. Подождите буквально минуту и магазин будет готов!'
                : 'We are deploying new changes to our store. One minute and they will go live!',
            \Includes\ErrorHandler::ERROR_MAINTENANCE_MODE
        );
    }

    /**
     * Return list of cache state indicator files
     *
     * @return array
     */
    protected static function getCacheStateFiles()
    {
        return array_map(['static', 'getCacheStateIndicatorFileName'], static::$steps);
    }

    /**
     * Move temporary directories to original directories
     *
     * @return void
     */
    protected static function moveTemporaryDirs()
    {
        if (static::isCapsular()) {
            // Rename directories
            $original = static::getCacheDirs(false);
            foreach (static::getCacheDirs(true) as $i => $tmpDir) {
                if (!rename($tmpDir, rtrim($tmpDir, LC_DS) . '.old.' . static::constructFileKey(true))) {
                    \Includes\Utils\FileManager::unlinkRecursive($tmpDir);
                }

                $originalDir = $original[$i];
                rename($originalDir, $tmpDir);
            }

            // Rename files
            $original = static::getDecoratorDataFiles(false);
            foreach (static::getDecoratorDataFiles(true) as $i => $tmpPath) {
                $destPath = $original[$i];
                if (file_exists($tmpPath)) {
                    rename($tmpPath, $destPath);
                }
            }
        }
    }

    // }}}

    // {{{ Common routines to run step handlers

    /**
     * Step started
     *
     * @param string $step Current step
     *
     * @return void
     */
    protected static function startStep($step)
    {
        static::$step = (int)$step;

        if (!LC_IS_CLI_MODE) {
            static::sendHeaders();
        }

        static::unsetProcessMark();

        if (static::STEP_FIRST === $step) {
            static::initializeRebuild();
        }

        static::showStepMessage(
            LC_IS_CLI_MODE ? static::getPlainMessage() : static::getHTMLMessage(),
            true,
            false,
            true
        );
    }

    /**
     * Send headers
     *
     * @return void
     */
    protected static function sendHeaders()
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Type: text/html; charset=utf-8');
        header('X-Robots-Tag: noindex, nofollow');
    }

    /**
     * Check if current step is last and redirect is prohibited after that step
     *
     * @param integer $step Current step
     *
     * @return boolean
     */
    protected static function isSkipRedirectAfterLastStep($step)
    {
        return static::LAST_STEP === $step && isset($_GET['doNotRedirectAfterCacheIsBuilt']);
    }

    /**
     * Check if only one step must be performed
     *
     * @return boolean
     */
    protected static function isDoOneStepOnly()
    {
        return defined('DO_ONE_STEP_ONLY');
    }

    /**
     * Step completed
     *
     * @param string $step Current step
     *
     * @return void
     */
    protected static function completeStep($step)
    {
        if (!static::$skipStepCompletion) {
            // "Step completed" indicator
            \Includes\Utils\FileManager::write(
                static::getCacheStateIndicatorFileName($step),
                static::getCacheStateIndicatorFileContent()
            );
        }

        if (static::LAST_STEP === $step) {
            static::finalizeRebuild();
        }

        if (static::isSkipRedirectAfterLastStep($step)) {
            // Do not redirect after last step
            // (this mode is used when cache builder was launched from LC standalone installation script)
            static::displayCompleteMessage();
            exit (0);

        } elseif (!static::isDoOneStepOnly()) {
            // Perform redirect (needed for multi-step cache generation)

            if (!LC_IS_CLI_MODE) {
                static::showStepMessage(static::getJSFinishMessage(), true, false);
            }

            //$arguments = [
            //    static::CPID_NAME => null,
            //    static::KEY_NAME  => null,
            //];
            //
            //if (static::LAST_STEP !== $step) {
            //    $arguments[static::CPID_NAME] = static::getRebuildIndicatorFileContent();
            //    if (static::isCapsular()) {
            //        $arguments[static::KEY_NAME] = static::getKey();
            //    }
            //}

            //if ('cli' === PHP_SAPI) {
            //    \Includes\Utils\Operator::refresh($arguments);
            //}
        }
    }

    /**
     * Initialize rebuild cache
     *
     * @return void
     */
    protected static function initializeRebuild()
    {
        // Put the indicator file
        \Includes\Utils\FileManager::write(
            static::getRebuildIndicatorFileName(),
            static::getRebuildIndicatorFileContent()
        );

        // Set initialize timestamp
        \Includes\Decorator\Utils\CacheInfo::set('initializeRebuild', time());
    }

    /**
     * Finalize rebuild cache
     *
     * @return void
     */
    protected static function finalizeRebuild()
    {
        // Block software before move directories
        static::setRebuildBlockMark(static::LAST_STEP);

        // Move non file datacache
        static::clearOldNonFileDatacache();

        // Set finalize timestamp
        \Includes\Decorator\Utils\CacheInfo::set('finalizeRebuild', time());

        // Remove cache info
        \Includes\Decorator\Utils\CacheInfo::remove();

        // Move temporary directories into original
        static::moveTemporaryDirs();

        // Remove the "rebuilding cache" indicator file
        static::removeRebuildIndicatorFile();

        // Remove rebuild mark
        static::unsetCacheRebuildMark();

        // Remove rebuild block mark
        static::unsetRebuildBlockMark();

        // Remove migration file
        SchemaMigrationManager::removeMigration();

        // Remove cache info #2
        \Includes\Decorator\Utils\CacheInfo::remove();
    }

    /**
     * Run a step callback
     *
     * @param string $step Step name
     *
     * @return array
     */
    protected static function getStepCallback($step)
    {
        return [get_called_class(), 'executeStepHandler' . (string)$step];
    }

    /**
     * Run a step
     *
     * @param string $step Step name
     *
     * @return void
     */
    public static function runStep($step)
    {
        // Set internal flag
        if (!defined('LC_CACHE_BUILDING')) {
            define('LC_CACHE_BUILDING', true);
        }

        if (!defined('LC_USE_CLEAN_URLS')) {
            define('LC_USE_CLEAN_URLS', false);
        }

        // Initialize logging
        static::initializeLogging();

        // To prevent multiple processes execution
//        static::checkIfRebuildStarted();

        // Write indicator files and show the message
        static::startStep($step);

        // Enable output (if needed)
//        static::setFastCGITimeoutEcho();

        // Set version key for view lists
//        static::setViewListsVersionKey();

        // Perform step-specific actions
        \Includes\Utils\Operator::executeWithCustomMaxExecTime(
            \Includes\Utils\ConfigParser::getOptions(['decorator', 'time_limit']),
            static::getStepCallback($step)
        );

        // (Un)Set indicator files and redirect
        static::completeStep($step);
    }

    /**
     * Run a step and return true if step is actually was performed or false if step has already been performed before
     *
     * @param string $step Step name
     *
     * @return boolean
     */
    protected static function runStepConditionally($step)
    {
        $result = false;

        if (static::isRebuildNeeded($step)) {
            static::runStep($step);
            $result = true;
        }

        return $result;
    }

    /**
     * Initialize logging
     *
     * @return void
     */
    protected static function initializeLogging()
    {
        set_error_handler(['Includes\ErrorHandler', 'handleCommonError']);
    }

    /**
     * Set view lists version key
     *
     * @return void
     */
    protected static function setViewListsVersionKey()
    {
        $steps = [
            static::STEP_FIRST,
            static::STEP_SECOND,
            static::STEP_THIRD,
            static::STEP_FOURTH,
            static::STEP_FIFTH
        ];

        if (!in_array(static::$step, $steps, true)
            && \Includes\Decorator\Utils\CacheManager::isCapsular()
        ) {
            \XLite\Model\ViewList::setVersionKey(\Includes\Decorator\Utils\CacheManager::getKey());
        }
    }

    /**
     * Clear old datacache
     *
     * @return void
     */
    protected static function clearOldNonFileDatacache()
    {
        $old = new \XLite\Core\Cache(null, ['original' => true]);
        if (!$old->getDriver() instanceof \XLite\Core\Cache\FilesystemCache) {
            $old->flushAll();
        }
    }

    // }}}

    // {{{ Step handlers

    /**
     * Run handler for the current step
     *
     * :NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler1()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_BEFORE_CLEANUP);

        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_FIRST);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler2()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_SECOND);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler3()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_THIRD);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler4()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_FOURTH);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler5()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_FIFTH);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler6()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_SIX);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler7()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_SEVEN);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler8()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_EIGHT);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler9()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_NINE);

        // Postprocess step (Quick data)
        if (\Includes\Decorator\Plugin\Doctrine\Plugin\QuickData\Main::isCalculateCacheAllowed()
            && \Includes\Decorator\Utils\CacheInfo::get('rebuildBlockMark')
        ) {
            $counter = \Includes\Decorator\Plugin\Doctrine\Plugin\QuickData\Main::getCounter();
            $productsCount = \XLite\Core\Database::getRepo('XLite\Model\Product')->count();
            if ($counter != $productsCount) {
                static::$skipStepCompletion = true;
            }
        }
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler10()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_TEN);
    }


    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler11()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_ELEVEN);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler12()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_TWELVE);
    }

    /**
     * Run handler for the current step
     *
     * NOTE: method is public since it's called from
     * \Includes\Utils\Operator::executeWithCustomMaxExecTime()
     *
     * @return void
     */
    public static function executeStepHandler13()
    {
        // Invoke plugins
        \Includes\Decorator\Utils\PluginManager::invokeHook(static::HOOK_STEP_THIRTEEN);
    }

    // }}}

    // {{{ Check permissions

    /**
     * Check directory permissions and try to correct them
     *
     * @param string $dir Path to check
     *
     * @return void
     */
    protected static function checkPermissions($dir)
    {
        \Includes\Utils\FileManager::mkdirRecursive($dir);

        if (!\Includes\Utils\FileManager::isDirWriteable($dir)) {
            @\Includes\Utils\FileManager::chmod($dir, static::getDirDefaultPermissions($dir));

            if (!\Includes\Utils\FileManager::isDirWriteable($dir)) {
                static::fireDirPermissionsError($dir);
            }
        }
    }

    /**
     * Fire the error them unable to set directory permissions
     *
     * @param string $dir Path to check
     *
     * @return void
     */
    protected static function fireDirPermissionsError($dir)
    {
        \Includes\ErrorHandler::fireError(static::getDirPermissionsErrorMessage($dir));
    }

    /**
     * Return permissions error message
     *
     * @param string $dir Path to check
     *
     * @return string
     */
    protected static function getDirPermissionsErrorMessage($dir)
    {
        return 'The "' . $dir . '" directory is not writeable. Please correct the permissions';
    }

    /**
     * Return default directory permissions
     *
     * @param string $dir Path to check
     *
     * @return integer
     */
    protected static function getDirDefaultPermissions($dir)
    {
        return 0755;
    }

    // }}}

    // {{{ Top-level methods

    /**
     * Main public method: rebuild classes cache
     *
     * @return void
     */
    public static function rebuildCache()
    {
        if (static::isRebuildAllowed()) {
            static::checkPermissions(LC_DIR_VAR);

            static::checkPermissions(static::getCompileDir());

            static::recreateHtaccessIfNeeded();

            static::checkRebuildBlock();

            static::setProcessMark();

            foreach (static::$steps as $step) {
                if (static::runStepConditionally($step) && static::isDoOneStepOnly()) {
                    // Break after first performed step if isDoOneStepOnly() returned true
                    break;
                }
            }
        } elseif (static::isRebuildInProgress() || static::isRebuildNeeded(1)) {
            static::triggerMaintenanceModeError();
        }
    }

    public static function recreateHtaccessIfNeeded()
    {
        if (!file_exists(LC_DIR_VAR . LC_DS . '.htaccess')) {
            file_put_contents(
                LC_DIR_VAR . LC_DS . '.htaccess',
                'Options -Indexes' . PHP_EOL
                . PHP_EOL
                . 'Deny from all' . PHP_EOL
                . PHP_EOL
                . '<Files ~ ".(?i:gif|jpe?g|png|bmp|css|js)$">' . PHP_EOL
                . '  Allow from all' . PHP_EOL
                . '</Files>' . PHP_EOL
            );
        }
    }

    /**
     * Return current step identifier
     *
     * @return int
     */
    public static function getCurrentStep()
    {
        return static::$step;
    }

    /**
     * Check if cache rebuild is needed
     *
     * @param int|null $step Current step OPTIONAL
     *
     * @return boolean
     */
    public static function isRebuildNeeded($step = null)
    {
        if ($step === null) {
            $step = static::getStep();
        }

        return $step
            && !\Includes\Utils\FileManager::isExists(static::getCacheStateIndicatorFileName($step));
    }

    /**
     * Clean up the cache
     *
     * @param boolean $cleanOriginalDirs Cleanup original directories OPTIONAL
     *
     * @return void
     */
    public static function cleanupCache($cleanOriginalDirs = false)
    {
        foreach (static::getCacheDirs($cleanOriginalDirs) as $dir) {
            if ($cleanOriginalDirs
                || static::getResourcesDir($cleanOriginalDirs) != $dir
            ) {
                \Includes\Utils\FileManager::unlinkRecursive($dir);
                static::checkPermissions($dir);
            }
        }
    }

    /**
     * Get cache directories
     *
     * @param boolean $originalDirs Get original directories OPTIONAL
     *
     * @return array
     */
    public static function getCacheDirs($originalDirs = false)
    {
        return [
            static::getCompileDir($originalDirs),
            static::getLocaleDir($originalDirs),
            static::getDatacacheDir($originalDirs),
            static::getTmpDir($originalDirs),
            static::getResourcesDir($originalDirs),
        ];
    }

    /**
     * Get decorator data files
     *
     * @param mixed $key Key OPTIONAL
     *
     * @return array
     */
    protected static function getDecoratorDataFiles($key = true)
    {
        return [
            \Includes\Decorator\Utils\CacheInfo::getFilename($key),
        ];
    }

    // }}}

    // {{{ Fix for the FastCGI timeout (http://bugtracker.qtmsoft.com/view.php?id=41139)

    /**
     * Set output per tick(s)
     *
     * @return void
     */
    protected static function setFastCGITimeoutEcho()
    {
        if (\Includes\Utils\ConfigParser::getOptions(['decorator', 'use_output'])) {
            declare(ticks=10000);

            register_tick_function(['\Includes\Utils\Operator', 'showMessage'], '.', false);
        }
    }

    // }}}

    // {{{ Cache key

    /**
     * Check - current cache rebuild process is capsular or not
     *
     * @return bool
     */
    public static function isCapsular()
    {
        return static::getKey()
            && isset($_REQUEST[static::KEY_NAME])
            && static::getKey() === $_REQUEST[static::KEY_NAME];
    }

    /**
     * Get cache rebuildprocess key
     *
     * @param boolean $overload Overload cached key value
     *
     * @return string
     */
    public static function getKey($overload = false)
    {
        if (!isset(static::$key) || $overload) {
            $key = static::getCacheRebuildMark();
            static::$key = (empty($key) || !preg_match(static::KEY_NAME_PATTERN, $key))
                ? null
                : $key;
        }

        return static::$key;
    }

    /**
     * Set key
     *
     * @param string $key Key
     *
     * @return void
     */
    protected static function setKey($key)
    {
        static::$key = null;

        if (preg_match(static::KEY_NAME_PATTERN, $key)) {
            static::$key = $key;
        }
    }

    /**
     * Generate key
     *
     * @return string
     */
    protected static function generateKey()
    {
        return md5(microtime() . mt_rand(1, 1000));
    }

    // }}}

    // {{{ Paths routines

    /**
     * Get compile directory
     *
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    public static function getCompileDir($original = false)
    {
        return $original ? LC_DIR_COMPILE : static::buildCopsularDirname(LC_DIR_COMPILE);
    }

    /**
     * Get locale directory
     *
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    public static function getLocaleDir($original = false)
    {
        return $original ? LC_DIR_LOCALE : static::buildCopsularDirname(LC_DIR_LOCALE);
    }

    /**
     * Get datracache directory
     *
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    public static function getDatacacheDir($original = false)
    {
        return $original ? LC_DIR_DATACACHE : static::buildCopsularDirname(LC_DIR_DATACACHE);
    }

    /**
     * Get resources directory
     *
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    public static function getResourcesDir($original = false)
    {
        return $original ? LC_DIR_CACHE_RESOURCES : static::buildCopsularDirname(LC_DIR_CACHE_RESOURCES);
    }

    /**
     * Get temporary directory
     *
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    public static function getTmpDir($original = false)
    {
        $dir = $original ? LC_DIR_TMP : static::buildCopsularDirname(LC_DIR_TMP);

        FileManager::touchDir($dir);

        return $dir;
    }

    /**
     * Build copsular dirname
     *
     * @param string $name File path
     * @param mixed $key Key OPTIONAL
     *
     * @return string
     */
    public static function buildCopsularDirname($name, $key = true)
    {
        if (static::isCapsular()) {
            $name = rtrim($name, LC_DS);
            $key = static::constructFileKey($key);
            if ($key) {
                $name .= '.' . $key;
            }
            $name .= LC_DS;
        }

        return $name;
    }

    /**
     * Build copsular filename
     *
     * @param string $name File path
     * @param mixed $key Key OPTIONAL
     *
     * @return string
     */
    public static function buildCopsularFilename($name, $key = true)
    {
        if (static::isCapsular()) {
            $key = static::constructFileKey($key);
            if ($key) {
                $name .= '.' . $key;
            }
        }

        return $name;
    }

    /**
     * Construct data key
     *
     * @param mixed $key Key
     *
     * @return string
     */
    protected static function constructFileKey($key)
    {
        if (true === $key) {
            $key = static::isCapsular() ? static::getKey() : static::CURRENT_FILE_KEY_SUFFIX;

        } elseif (false === $key) {
            $key = static::CURRENT_FILE_KEY_SUFFIX;
        }

        return $key;
    }

    // }}}

    // {{{ Data cache namespace suffix routines

    /**
     * Get datacache suffix
     *
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    public static function getDataCacheSuffix($original = false)
    {
        $suffix = \Includes\Utils\FileManager::read(
            static::getDataCacheSuffixFileName($original)
        );

        if (!$suffix) {
            $suffix = static::generateDataCacheSuffix();
            static::setDataCacheSuffix($suffix, $original);
        }

        return $suffix;
    }

    /**
     * Set datacache suffix
     *
     * @param string $suffix Suffix
     * @param boolean $original Get original path OPTIONAL
     *
     * @return void
     */
    public static function setDataCacheSuffix($suffix, $original = false)
    {
        \Includes\Utils\FileManager::write(
            static::getDataCacheSuffixFileName($original),
            $suffix
        );
    }

    /**
     * Generate datacache suffix
     *
     * @return string
     */
    protected static function generateDataCacheSuffix()
    {
        return uniqid('data_cache', true);
    }

    /**
     * Get data cache suffix file path
     *
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    protected static function getDataCacheSuffixFileName($original = false)
    {
        return static::getCompileDir($original) . '.datacacheSuffix';
    }

    // }}}

    // {{{ Rebuild block mark routine

    /**
     * Check - rebuild block mark is set or not
     *
     * @return boolean
     */
    public static function isRebuildBlock()
    {
        return \Includes\Utils\FileManager::isExists(
            static::getRebuildBlockMarkFilePath()
        );
    }

    /**
     * Set rebuild block mark
     *
     * @param mixed $step Step
     * @param array $data Additiuonal data OPTIONAL
     *
     * @return void
     */
    public static function setRebuildBlockMark($step, array $data = [])
    {
        if (!static::isRebuildBlock()) {
            if (!\Includes\Decorator\Utils\CacheInfo::get('rebuildBlockMark')) {
                \Includes\Decorator\Utils\CacheInfo::set(
                    'rebuildBlockMark',
                    [
                        'step' => $step,
                        'time' => time(),
                    ] + $data
                );
            }

            \Includes\Utils\FileManager::write(
                static::getRebuildBlockMarkFilePath(),
                static::getRebuildBlockMarkContent()
            );

            // Wait other processes
            $count = 10;
            while (static::isExistsProcessMarks() && $count > 0) {
                sleep(1);
                $count--;
            }

            static::cleanupProcessMarks();
        }
    }

    /**
     * Unset rebuild block mark
     *
     * @return void
     */
    public static function unsetRebuildBlockMark()
    {
        \Includes\Utils\FileManager::deleteFile(
            static::getRebuildBlockMarkFilePath()
        );
    }

    /**
     * Check rebuild block
     *
     * @return void
     */
    public static function checkRebuildBlock()
    {
        // To prevent multiple processes execution
        static::checkIfRebuildStarted();
    }

    /**
     * Get rebuild block mark file path
     *
     * @return string
     */
    protected static function getRebuildBlockMarkFilePath()
    {
        return LC_DIR_VAR . '.rebuildBlockMark';
    }

    /**
     * Get rebuild block mark content
     *
     * @return string
     */
    protected static function getRebuildBlockMarkContent()
    {
        return static::getKey() ?: '_';
    }

    // }}}

    // {{{ Current process mark

    /**
     * Check - is exists process marks
     *
     * @return boolean
     */
    public static function isExistsProcessMarks()
    {
        $list = glob(LC_DIR_VAR . '.process.*');

        return !empty($list);
    }

    /**
     * Cleanup process marks
     *
     * @return void
     */
    public static function cleanupProcessMarks()
    {
        $list = glob(LC_DIR_VAR . '.process.*');
        if ($list) {
            foreach ($list as $path) {
                \Includes\Utils\FileManager::deleteFile($path);
            }
        }
    }

    /**
     * Unset process mark
     *
     * @return void
     */
    public static function unsetProcessMark()
    {
        $path = static::getProcessMarkPath();
        if (\Includes\Utils\FileManager::isExists($path)) {
            \Includes\Utils\FileManager::deleteFile($path);
        }
    }

    /**
     * Set process mark
     *
     * @return void
     */
    protected static function setProcessMark()
    {
        if (static::getKey()) {
            \Includes\Utils\FileManager::write(static::getProcessMarkPath(), '');
            register_shutdown_function(
                function () {
                    \Includes\Decorator\Utils\CacheManager::unsetProcessMark();
                }
            );
        }
    }

    /**
     * Get process mark file path
     *
     * @return string
     */
    protected static function getProcessMarkPath()
    {
        return LC_DIR_VAR . '.process.' . getmypid();
    }

    // }}}
}
