<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\IntegrityCheck;

use XCart\Bus\System\Filesystem;

class CoreFilterIterator extends \RecursiveFilterIterator
{
    /**
     * Exclude pattern
     *
     * @var string
     */
    protected $excludePattern;

    /**
     * Include pattern
     *
     * @var string
     */
    protected $includePattern;

    /**
     * @var string
     */
    protected $mandatoryPattern;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param \RecursiveIterator $iterator
     * @param string             $rootDir
     * @param Filesystem         $filesystem
     * @param                    $mandatoryPattern
     * @param                    $excludePattern
     * @param                    $includePattern
     */
    public function __construct(
        $iterator,
        $rootDir,
        Filesystem $filesystem,
        $mandatoryPattern,
        $excludePattern,
        $includePattern
    ) {
        parent::__construct($iterator);
        $this->rootDir = $rootDir;
        $this->filesystem = $filesystem;
        $this->mandatoryPattern = $mandatoryPattern;
        $this->excludePattern = $excludePattern;
        $this->includePattern = $includePattern;
    }

    public function getChildren() {
        return new self(
            $this->getInnerIterator()->getChildren(),
            $this->rootDir,
            $this->filesystem,
            $this->mandatoryPattern,
            $this->excludePattern,
            $this->includePattern
        );
    }

    /**
     * Check whether the current element of the iterator is acceptable
     * @link  http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     * @since 5.1.0
     */
    public function accept()
    {
        $current = $this->current();
        $path = $current->getPathname();

        /** @var \SplFileInfo $item */
        $prefix = $this->rootDir;

        if (0 === strpos($path, $prefix)) {
            $path = substr($path, strlen($prefix));
        }

        return (!preg_match($this->excludePattern, $path)
                || preg_match($this->includePattern, $path)
            ) && (strpos($path, 'skins') === false || $path === 'skins' || preg_match($this->mandatoryPattern, $path));
    }
}
