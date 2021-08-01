<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Logger;

final class Backtrace
{
    /**
     * @var array
     */
    private $backtrace;

    /**
     * @var array
     */
    private $pathAliases = [];

    /**
     * @param array $backtrace
     * @param array $options
     */
    public function __construct(array $backtrace = [], array $options = [])
    {
        $offset = isset($options['offset']) ? $options['offset'] : 0;

        $this->backtrace = $backtrace ?: array_slice(debug_backtrace(false), $offset);

        $pathAliases = isset($options['path_aliases']) && is_array($options['path_aliases'])
            ? $options['path_aliases']
            : [];

        foreach ($pathAliases as $path => $alias) {
            $this->pathAliases['/^' . preg_quote($path, '/') . '/'] = $alias;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->prepareBacktrace($this->backtrace);
    }

    /**
     * @param array $backtrace
     *
     * @return string
     */
    private function prepareBacktrace(array $backtrace = [])
    {
        $trace = [];

        foreach ($backtrace as $frame) {
            $parts = [];

            if (isset($frame['file'])) {
                $parts[] = 'file '
                    . $this->prepareFilePath($frame['file'])
                    . (isset($frame['line']) ? ('(' . $frame['line'] . ')') : '');
            }

            if (isset($frame['class'], $frame['function'])) {
                $parts[] = $frame['class']
                    . (isset($frame['type']) ? $frame['type'] : '::')
                    . $frame['function']
                    . '()';

            } elseif (isset($frame['function'])) {
                $parts[] = $frame['function'] . '()';
            }

            if ($parts) {
                $trace[] = implode(' : ', $parts);
            }
        }

        return implode("\n", $trace);
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    private function prepareFilePath($filePath)
    {
        if ($this->pathAliases) {
            $filePath = preg_replace(array_keys($this->pathAliases), array_values($this->pathAliases), $filePath);
        }

        return $filePath;
    }
}
