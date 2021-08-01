<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\System;

interface FilesystemInterface
{
    /**
     * @param string $originFile          The original filename
     * @param string $targetFile          The target filename
     * @param bool   $overwriteNewerFiles If true, target files newer than origin files are overwritten
     */
    public function copy($originFile, $targetFile, $overwriteNewerFiles = false);

    /**
     * @param string|iterable $dirs The directory path
     * @param int             $mode The directory mode
     */
    public function mkdir($dirs, $mode = 0777);

    /**
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return bool true if the file exists, false otherwise
     */
    public function exists($files);

    /**
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to create
     * @param int             $time  The touch time as a Unix timestamp
     * @param int             $atime The access time as a Unix timestamp
     */
    public function touch($files, $time = null, $atime = null);

    /**
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     */
    public function remove($files);

    /**
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change mode
     * @param int             $mode      The new mode (octal)
     * @param int             $umask     The mode mask (octal)
     * @param bool            $recursive Whether change the mod recursively or not
     */
    public function chmod($files, $mode, $umask = 0000, $recursive = false);

    /**
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change owner
     * @param string          $user      The new owner user name
     * @param bool            $recursive Whether change the owner recursively or not
     */
    public function chown($files, $user, $recursive = false);

    /**
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change group
     * @param string          $group     The group name
     * @param bool            $recursive Whether change the group recursively or not
     */
    public function chgrp($files, $group, $recursive = false);

    /**
     * @param string $origin    The origin filename or directory
     * @param string $target    The new filename or directory
     * @param bool   $overwrite Whether to overwrite the target if it already exists
     */
    public function rename($origin, $target, $overwrite = false);

    /**
     * @param string $originDir     The origin directory path
     * @param string $targetDir     The symbolic link name
     * @param bool   $copyOnWindows Whether to copy files if on Windows
     */
    public function symlink($originDir, $targetDir, $copyOnWindows = false);

    /**
     * @param string          $originFile  The original file
     * @param string|string[] $targetFiles The target file(s)
     */
    public function hardlink($originFile, $targetFiles);

    /**
     * @param string $path         A filesystem path
     * @param bool   $canonicalize Whether or not to return a canonicalized path
     *
     * @return string|null
     */
    public function readlink($path, $canonicalize = false);

    /**
     * @param string $endPath   Absolute path of target
     * @param string $startPath Absolute path where traversal begins
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative($endPath, $startPath);

    /**
     * @param string       $originDir The origin directory
     * @param string       $targetDir The target directory
     * @param \Traversable $iterator  A Traversable instance
     * @param array        $options   An array of boolean options
     *                                Valid options are:
     *                                - $options['override'] Whether to override an existing file on copy or not (see copy())
     *                                - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink())
     *                                - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     */
    public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = []);

    /**
     * @param string $file A file path
     *
     * @return bool
     */
    public function isAbsolutePath($file);

    /**
     * @param string $dir    The directory where the temporary filename will be created
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function tempnam($dir, $prefix);

    /**
     * @param string $filename The file to be written to
     * @param string $content  The data to write into the file
     */
    public function dumpFile($filename, $content);

    /**
     * @param string $filename The file to which to append content
     * @param string $content  The content to append
     */
    public function appendToFile($filename, $content);

    /**
     * @param string $path
     *
     * @return string
     */
    public function getNearestExistingDirectory($path): string;
}
