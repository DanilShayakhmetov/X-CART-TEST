<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ConfigParser;

class ConfigFile
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private $data;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @return array
     * @throws ConfigMissingFileException
     * @throws ConfigWrongFormattedFileException
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->loadFromFile();
        }

        return $this->data;
    }

    /**
     * @param string $section
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function setOption($section, $name, $value)
    {
        $content = file_get_contents($this->file);

        if (preg_match("/^{$name}.*=.*/m", $content)) {
            $content = preg_replace("/^{$name}.*=.*/m", "{$name} = {$value}", $content);
        } elseif (preg_match("/^\[{$section}\].*$/m", $content)) {
            $content = preg_replace("/^(\[{$section}\].*)$/m", "\\1\n{$name} = {$value}\n", $content);
        }

        $result = file_put_contents($this->file, $content);

        return $result !== false;
    }

    /**
     * @throws ConfigMissingFileException
     * @throws ConfigWrongFormattedFileException
     */
    private function loadFromFile()
    {
        if (!$this->isReadable()) {
            throw ConfigMissingFileException::fromMissingFile($this->file);
        }

        $data = parse_ini_file($this->file, true);

        if (!is_array($data)) {
            throw ConfigWrongFormattedFileException::fromWrongFormattedFile($this->file);
        }

        $this->data = $data;
    }

    /**
     * @return bool
     */
    private function isReadable()
    {
        return (is_file($this->file) || is_link($this->file)) && is_readable($this->file);
    }
}
