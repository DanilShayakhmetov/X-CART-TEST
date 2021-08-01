<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * Converter
 *
 * @package    XLite
 */
abstract class Converter extends \Includes\Utils\AUtils
{
    /**
     * Method name translation records
     *
     * @var array
     */
    protected static $to = [
        'Q',
        'W',
        'E',
        'R',
        'T',
        'Y',
        'U',
        'I',
        'O',
        'P',
        'A',
        'S',
        'D',
        'F',
        'G',
        'H',
        'J',
        'K',
        'L',
        'Z',
        'X',
        'C',
        'V',
        'B',
        'N',
        'M',
    ];

    /**
     * Method name translation patterns
     *
     * @var array
     */
    protected static $from = [
        '_q',
        '_w',
        '_e',
        '_r',
        '_t',
        '_y',
        '_u',
        '_i',
        '_o',
        '_p',
        '_a',
        '_s',
        '_d',
        '_f',
        '_g',
        '_h',
        '_j',
        '_k',
        '_l',
        '_z',
        '_x',
        '_c',
        '_v',
        '_b',
        '_n',
        '_m',
    ];

    /**
     * File size suffixes.
     * Source: http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
     * Source: http://physics.nist.gov/cuu/Units/binary.html
     *
     * @var array
     */
    protected static $byteMultipliers = ['b', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    /**
     * Generate query string
     *
     * @param array  $data      data to use
     * @param string $glue      string to add between param name and value
     * @param string $separator string to separate <name,value> pairs
     * @param string $quotes    char (string) to quote the value
     *
     * @return string
     */
    public static function buildQuery(array $data, $glue = '=', $separator = '&', $quotes = '')
    {
        $result = [];

        foreach ($data as $name => $value) {
            $result[] = $name . $glue . $quotes . $value . $quotes;
        }

        return implode($separator, $result);
    }

    /**
     * Parse arguments array
     *
     * @param array   $args     Array to parse
     * @param string  $glue     Char to agglutinate "name" and "value"
     * @param string  $quotes   Char to quote the "value" param
     * @param boolean $hasParts Flag OPTIONAL
     *
     * @return array
     */
    public static function parseArgs(array $args, $glue = '=', $quotes = '', $hasParts = true)
    {
        if (!isset($glue)) {
            $glue = '=';
        }

        $result = [];

        foreach ($args as $part) {

            if (!$hasParts) {
                $result[] = trim(trim($part), $quotes);

            } elseif (1 < count($tokens = explode($glue, trim($part)))) {
                $result[$tokens[0]] = trim($tokens[1], $quotes);
            }
        }

        return $result;
    }

    /**
     * Parse string into array
     *
     * @param string  $query     Query
     * @param string  $glue      Char to agglutinate "name" and "value"
     * @param string  $separator Char to agglutinate <"name", "value"> pairs
     * @param string  $quotes    Char to quote the "value" param
     * @param boolean $hasParts  Flag OPTIONAL
     *
     * @return array
     */
    public static function parseQuery($query, $glue = '=', $separator = '&', $quotes = '', $hasParts = true)
    {
        return static::parseArgs(explode($separator, $query), $glue, $quotes, $hasParts);
    }

    /**
     * Remove leading characters from string
     *
     * @param string $string string to prepare
     * @param string $chars  charlist to remove
     *
     * @return string
     */
    public static function trimLeadingChars($string, $chars)
    {
        return ltrim($string, $chars);
    }

    /**
     * Remove trailing characters from string
     *
     * @param string $string string to prepare
     * @param string $chars  charlist to remove
     *
     * @return string
     */
    public static function trimTrailingChars($string, $chars)
    {
        return rtrim($string, $chars);
    }

    /**
     * Get formatted price
     *
     * @param float $price value to format
     *
     * @return string
     */
    public static function formatPrice($price)
    {
        return sprintf('%.02f', round(doubleval($price), 2));
    }

    /**
     * Convert a string like "test_foo_bar" into the camel case (like "testFooBar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToCamelCase($string)
    {
        return ucfirst(str_ireplace(self::$from, self::$to, strval($string)));
    }

    /**
     * Convert a string like "testFooBar" into the underline style (like "test_foo_bar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertFromCamelCase($string)
    {
        return str_replace(self::$to, self::$from, lcfirst(strval($string)));
    }

    /**
     * Convert a string like "test_foo_bar" into the Pascal case (like "TestFooBar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToPascalCase($string)
    {
        return ucfirst(static::convertToCamelCase($string));
    }

    /**
     * Get canonical form of class name
     *
     * @param string  $class    Class name to prepare
     * @param boolean $relative Flag to enclose class name with namespace separator
     *
     * @return string
     */
    public static function prepareClassName($class, $relative = true)
    {
        return ($relative ? '' : '\\') . static::trimLeadingChars($class, '\\');
    }

    /**
     * Get file name by PHP class name
     *
     * @param string $class Class name
     *
     * @return string
     */
    public static function getClassFile($class)
    {
        return str_replace('\\', LC_DS, static::trimLeadingChars($class, '\\')) . '.php';
    }

    /**
     * Get full version
     *
     * @param string $versionMajor Major version
     * @param string $versionMinor Minor version
     *
     * @return string
     */
    public static function composeVersion($versionMajor, $versionMinor)
    {
        return $versionMajor . '.' . $versionMinor;
    }

    /**
     * Parse version and return array contained minorVersion and build number
     *
     * @param string $version Version (e.g '0', '1.1')
     *
     * @return array
     */
    public static function parseMinorVersion($version)
    {
        if (preg_match('/^(\d+)(?:\.(\d+))$/', $version, $match)) {
            $version = $match[1];
            $build = !empty($match[2]) ? $match[2] : 0;

        } else {
            $build = 0;
        }

        return [$version, $build];
    }

    /**
     * Prepare human-readable output for file size
     *
     * @param integer $size      Size in bytes
     * @param string  $separator To return a string OPTIONAL
     *
     * @return string
     */
    public static function formatFileSize($size, $separator = null)
    {
        $multiplier = 0;

        while (1000 < $size) {

            // http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
            // http://physics.nist.gov/cuu/Units/binary.html
            $size /= 1000;

            $multiplier++;
        }

        // Do not display numbers after decimal point if size is in kilobytes.
        // When size is greater than display one number after decimal point.
        $result = [number_format($size, $multiplier > 1 ? 1 : 0), static::$byteMultipliers[$multiplier]];

        return isset($separator) ? implode($separator, $result) : $result;
    }

    /**
     * Convert strings like 1M, 512K and so on to bytes size
     *
     * @param string $sizeStr String represantation of filesize
     *
     * @return integer
     */
    public static function returnBytesIniGetSize($sizeStr)
    {
        switch (substr($sizeStr, -1)) {
            case 'M':
            case 'm':
                return (int)$sizeStr * 1048576;
            case 'K':
            case 'k':
                return (int)$sizeStr * 1024;
            case 'G':
            case 'g':
                return (int)$sizeStr * 1073741824;
            default:
                return $sizeStr;
        }
    }

    /**
     * Remove \r and \n chars from string (e.g to prevent CRLF injections)
     *
     * @param string $value Input value
     *
     * @return string
     */
    public static function removeCRLF($value)
    {
        return trim(preg_replace('/[\r\n]+/', '', ((string)$value)));
    }

    /*
     * Compose URL from target, action and additional params
     *
     * @param string $target    Page identifier OPTIONAL
     * @param string $action    Action to perform OPTIONAL
     * @param array  $params    Additional params OPTIONAL
     * @param string $interface Interface script OPTIONAL
     *
     * @return string
     */
    public static function buildURL($target = '', $action = '', array $params = [], $interface = null)
    {
        $result = strval($interface);
        $urlParams = [];

        if (!empty($target)) {
            $urlParams['target'] = $target;
        }

        if (!empty($action)) {
            $urlParams['action'] = $action;
        }

        $params = $urlParams + $params;

        if (!empty($params)) {
            $result .= '?' . http_build_query($params, '', '&');
        }

        return $result;
    }

    /*
     *  Convert a string like "testFooBar" to translit
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToTranslit($string)
    {
        return \URLify::transliterate($string);
    }

    /**
     * Normalize string to avoid grapheme cluster boundaries
     * @see http://www.unicode.org/reports/tr29/#Grapheme_Cluster_Boundaries
     *
     * @param string $string String to normalize
     *
     * @return string
     */
    public static function normalizeUTF8($string)
    {
        return class_exists('Normalizer')
            ? \Normalizer::normalize($string, \Normalizer::FORM_KC)
            : $string;
    }
}
