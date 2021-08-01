<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\System;

use Closure;

interface FinderInterface
{
    /**
     * @return static|Finder
     */
    public function build();

    /**
     * @return Finder A new Finder instance
     */
    public static function create();

    /**
     * @return Finder The current Finder instance
     */
    public function directories();

    /**
     * @return Finder The current Finder instance
     */
    public function files();

    /**
     * @param  int $level The depth level expression
     *
     * @return Finder The current Finder instance
     */
    public function depth($level);

    /**
     * @param  string $date A date rage string
     *
     * @return Finder The current Finder instance
     */
    public function date($date);

    /**
     * @param  string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return Finder The current Finder instance
     */
    public function name($pattern);

    /**
     * @param  string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return Finder The current Finder instance
     */
    public function notName($pattern);

    /**
     * @param string $size A size range string
     *
     * @return Finder The current Finder instance
     */
    public function size($size);

    /**
     * @param  string $dir A directory to exclude
     *
     * @return Finder The current Finder instance
     */
    public function exclude($dir);

    /**
     * @param Boolean $ignoreDotFiles Whether to exclude "hidden" files or not
     *
     * @return Finder The current Finder instance
     */
    public function ignoreDotFiles($ignoreDotFiles);

    /**
     * @param Boolean $ignoreVCS Whether to exclude VCS files or not
     *
     * @return Finder The current Finder instance
     */
    public function ignoreVCS($ignoreVCS);

    public static function addVCSPattern($pattern);

    /**
     * @param  Closure $closure An anonymous function
     *
     * @return Finder The current Finder instance
     */
    public function sort(\Closure $closure);

    /**
     * @return Finder The current Finder instance
     */
    public function sortByName();

    /**
     * @return Finder The current Finder instance
     */
    public function sortByType();

    /**
     * @param  Closure $closure An anonymous function
     *
     * @return Finder The current Finder instance
     */
    public function filter(\Closure $closure);

    /**
     * @return Finder The current Finder instance
     */
    public function followLinks();

    /**
     * @param  string|array $dirs A directory path or an array of directories
     *
     * @return Finder The current Finder instance
     *
     * @throws \InvalidArgumentException if one of the directory does not exist
     */
    public function in($dirs);

    /**
     * @return \Iterator An iterator
     *
     * @throws \LogicException if the in() method has not been called
     */
    public function getIterator();

    /**
     * @param mixed $iterator
     */
    public function append($iterator);
}
