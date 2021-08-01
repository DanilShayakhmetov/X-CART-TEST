<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Utils;

use Includes\Utils\Module\Manager;

/**
 * Operator
 *
 */
abstract class Operator extends \Includes\Decorator\Utils\AUtils
{
    /**
     * Tags to ignore
     *
     * @var array
     */
    protected static $ignoredTags = array('see', 'since');

    // {{ Classes tree

    /**
     * Get iterator for class files
     *
     * @return \Includes\Utils\FileFilter
     */
    public static function getClassFileIterator()
    {
        return new \Includes\Utils\FileFilter(
            static::getClassesDir(),
            Manager::getPathPatternForPHP(static::getClassesDir())
        );
    }

    // }}}

    // {{{ Tags parsing

    /**
     * Parse dockblock to get tags
     *
     * @param string $content String to parse
     * @param array  $tags    Tags to search OPTIONAL
     *
     * @return array
     */
    public static function getTags($content, array $tags = array())
    {
        $result = array();

        if (preg_match_all(static::getTagPattern($tags), $content, $matches)) {
            $tags = static::parseTags($matches);

            if (!empty($tags)) {
                $result += static::parseTags($matches);
            }
        }

        return $result;
    }

    /**
     * Return pattern to parse source for tags
     *
     * @param array $tags List of tags to search
     *
     * @return string
     */
    public static function getTagPattern(array $tags)
    {
        return '/@\s*(' . (empty($tags) ? '\w+' : implode('|', $tags)) . ')(?=\s*)([^@\n]*)?/Smi';
    }

    /**
     * Parse dockblock to get tags
     *
     * @param array $matches Data from preg_match_all()
     *
     * @return array
     */
    protected static function parseTags(array $matches)
    {
        $result = array(array(), array());

        // Sanitize data
        array_walk($matches[2], function (&$value) { $value = trim(trim($value), ')('); });

        // There are so called "multiple" tags
        foreach (array_unique($matches[1]) as $tag) {

            // Ignore some time to save memory and time
            if (in_array($tag, static::$ignoredTags)) continue;

            // Check if tag is defined only once
            if (1 < count($keys = array_keys($matches[1], $tag))) {
                $list = array();

                // Convert such tag values into the single array
                foreach ($keys as $key) {

                    // Parse list of tag attributes and their values
                    $list[] = static::parseTagValue($matches[2][$key]);
                }

                // Add tag name and its values to the end of tags list.
                // All existing entries for this tag was cleared by the "unset()"
                $result[0][] = $tag;
                $result[1][] = $list;

            // If the value was parsed (the corresponded tokens were found), change its type to the "array"
            } elseif ($matches[2][$key = array_shift($keys)] !== ($value = static::parseTagValue($matches[2][$key]))) {

                $result[0][] = $tag;
                $result[1][] = array($value ?: $matches[2][$key]);
            }
        }

        // Create an associative array of tag names and their values
        return !empty($result[0]) && !empty($result[1])
            ? array_combine(array_map('strtolower', $result[0]), $result[1])
            : array();
    }

    /**
     * Parse value of a phpDocumenter tag
     *
     * @param string $value Value to parse
     *
     * @return array
     */
    protected static function parseTagValue($value)
    {
        return \Includes\Utils\Converter::parseQuery($value, '=', ',', '"\'');
    }

    // }}}
}
