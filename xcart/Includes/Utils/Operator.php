<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * Operator
 *
 * @package    XLite
 */
abstract class Operator extends \Includes\Utils\AUtils
{
    /**
     * Return length of the "dummy" buffer for flush
     *
     * @return int
     */
    protected static function getDummyBufferLength()
    {
        return 4096;
    }

    /**
     * Perform the "flush" itself
     *
     * @return void
     */
    protected static function flushBuffers()
    {
        if (ob_get_level()) {
            @ob_flush();
        }

        flush();
    }

    /**
     * Wrap message into some HTML tags (to fast output)
     *
     * @param string $message  Message to prepare
     * @param string $jsOutput JS output
     *
     * @return string
     */
    protected static function getJSMessage($message, $jsOutput)
    {
        return '<noscript>' . $message . '</noscript>'
             . '<script type="text/javascript">' . $jsOutput . '</script>';
    }


    /**
     * Redirect
     *
     * @param string $location        URL
     * @param int    $code            operation code
     * @param bool   $forceJsRedirect Force JS redirect
     *
     * @return void
     */
    public static function redirect($location, $code = 302, $forceJsRedirect = false)
    {
        $location = \Includes\Utils\Converter::removeCRLF($location);

        if ('cli' !== PHP_SAPI) {
            if ($forceJsRedirect || headers_sent()) {
                $message  = '<a href="' . $location . '">Click here to redirect</a>';
                $jsOutput = 'self.location = \'' . $location . '\';';

                static::flush($message, true, $jsOutput);

            } else {
                header('Location: ' . $location, true, $code);
            }
        }

        exit (0);
    }

    /**
     * Refresh current page
     *
     * @param array $params           Additional parameters OPTIONAL
     * @param bool  $forceJsRedirect Forece JS redirect
     *
     * @return void
     */
    public static function refresh($params = array(), $forceJsRedirect = false)
    {
        // Get current URL
        $url = \Includes\Utils\URLManager::getSelfURI();

        $query = parse_url($url, PHP_URL_QUERY);
        $url = str_replace('?' . $query, '', $url);
        $fragment = parse_url($url, PHP_URL_FRAGMENT);
        $url = str_replace('#' . $fragment, '', $url);

        $parsedQuery = array();
        parse_str($query, $parsedQuery);

        // Prepare additional URL parameters string
        foreach ($params as $k => $v) {
            if (isset($v)) {
                $parsedQuery[$k] = $v;

            } elseif (isset($parsedQuery[$k])) {
                unset($parsedQuery[$k]);
            }
        }

        if ($parsedQuery) {
            $url .= '?' . http_build_query($parsedQuery, null, '&');
        }

        if ($fragment) {
            $url .= '#' . $fragment;
        }

        // Do redirection
        static::redirect($url, 302, $forceJsRedirect);
    }

    /**
     * Echo message and flush output
     *
     * @param string  $message    Text to display
     * @param boolean $dummyFlush Output extra spaces or not OPTIONAL
     * @param string  $jsOutput   Flag to quick output OPTIONAL
     *
     * @return void
     */
    public static function flush($message, $dummyFlush = false, $jsOutput = null)
    {
        if ('cli' !== PHP_SAPI) {
            // Send extra whitespace before flushing
            if ($dummyFlush) {
                static::pureEcho(str_repeat(' ', static::getDummyBufferLength()));
            }

            // Wrap message into the "<script>" tag
            if (isset($jsOutput)) {
                $message = static::getJSMessage($message, $jsOutput);
            }
        }

        // Print message
        static::pureEcho($message);

        static::flushBuffers();
    }

    /**
     * Echo message
     *
     * @param string  $message    Text to display
     * @param boolean $dummyFlush Output extra spaces or not OPTIONAL
     * @param string  $jsOutput   Flag to quick output OPTIONAL
     *
     * @return void
     */
    public static function echoWithoutFlush($message, $dummyFlush = false, $jsOutput = null)
    {
        if ('cli' !== PHP_SAPI) {
            // Send extra whitespace before flushing
            if ($dummyFlush) {
                static::pureEcho(str_repeat(' ', static::getDummyBufferLength()));
            }

            // Wrap message into the "<script>" tag
            if (isset($jsOutput)) {
                $message = static::getJSMessage($message, $jsOutput);
            }
        }

        // Print message
        static::pureEcho($message);
    }

    /**
     * Echoes the message if it is not in AJAX mode
     *
     * @param string $message Message
     *
     * @return void
     */
    public static function pureEcho($message)
    {
        echo ($message);
    }

    /**
     * Wrapper to message quick display
     *
     * @param string  $message    Message text
     * @param boolean $addNewline Flag OPTIONAL
     * @param boolean $addJS      Add message in javascript wrapper OPTIONAL
     *
     * @return void
     */
    public static function showMessage($message, $addNewline = true, $addJS = true)
    {
        static::echoWithoutFlush(
            $message,
            true,
            $addJS ? 'document.write(\'' . $message . '\');' : null
        );

        if ($addNewline) {
            static::echoWithoutFlush(LC_EOL);
        }
    }

    /**
     * Check - is AJAX request or not
     *
     * @return boolean
     */
    public static function isAJAX()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * Set custom value for the "max_execution_time" INI setting, and execute some function
     *
     * @param int   $time     time (in seconds) to set
     * @param mixed $callback function to execute
     * @param array $args     call arguments
     *
     * @return mixed
     */
    public static function executeWithCustomMaxExecTime($time, $callback, array $args = array())
    {
        $savedValue = @ini_get('max_execution_time');
        @set_time_limit($time);

        $result = call_user_func_array($callback, $args);

        if (!empty($savedValue)) {
            @set_time_limit($savedValue);
        }

        return $result;
    }

    /**
     * Check if class is already declared.
     * NOTE: this function does not use autoloader
     *
     * @param string $name Class name
     *
     * @return boolean
     */
    public static function checkIfClassExists($name)
    {
        $result = class_exists($name, false);

        if (!$result) {
            $result = \Includes\Utils\FileManager::isFileReadable(
                \Includes\Autoloader::getLCAutoloadDir() . \Includes\Utils\Converter::getClassFile($name)
            );
        }

        return $result;
    }

    /**
     * Check if class is an LC one
     *
     * @param string $name Class name
     *
     * @return boolean
     */
    public static function checkIfLCClass($name)
    {
        return 0 === strpos(\Includes\Utils\Converter::prepareClassName($name), LC_NAMESPACE);
    }

    /**
     * Save service YAML
     *
     * @param string $path File path
     * @param array  $data Data
     *
     * @return integer
     */
    public static function saveServiceYAML($path, array $data)
    {
        return \Includes\Utils\FileManager::write(
            $path,
            static::getServiceHeader() . \Symfony\Component\Yaml\Yaml::dump($data)
        );
    }

    /**
     * Load service YAML
     *
     * @param string $path File path
     *
     * @return mixed
     */
    public static function loadServiceYAML($path)
    {
        $data = null;

        if (\Includes\Utils\FileManager::isFile($path)) {
            $data = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($path));
        }

        return $data;
    }

    /**
     * Get data storage service header
     *
     * @return string
     */
    public static function getServiceHeader()
    {
        return '# <' . '?php if (!defined(\'LC_DS\')) { die(); } ?' . '>' . PHP_EOL . PHP_EOL;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public static function generateHash($length = 64)
    {
        if ($length <= 0) {
            return '';
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return mb_substr(bin2hex(openssl_random_pseudo_bytes(round($length/2))), 0, $length);
        }

        $result = md5(microtime(true) + mt_rand(0, 1000000));

        while (mb_strlen($result) < $length) {
            $result .= md5(microtime(true) + mt_rand(0, 1000000));
        }

        return mb_substr($result, 0, $length);
    }

    /**
     * Purify link
     *
     * @param string $link String
     *
     * @return string
     */
    public static function purifyLink($link)
    {
        if (preg_match('/^{.*}$/', $link)) {
            return $link;
        }

        $html = "<a href='{$link}'>";
        $purifiedHtml = \XLite\Core\HTMLPurifier::purify($html);

        preg_match('/<a href="(.*)">/', $purifiedHtml, $matches);
        $purifiedLink = $matches[1];

        return $purifiedLink;
    }
}
