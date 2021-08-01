<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ConfigParser;

class ConfigParser
{
    /**
     * The copy of $_SERVER global
     *
     * @var array
     */
    private $environment;

    /**
     * @var string
     */
    private $configStorage;

    /**
     * @var array
     */
    private $files = [
        'config.dev.php',
        'config.php',
        'config.personal.php',
        'config.local.php',
    ];

    /**
     * @var ConfigPostProcessor
     */
    private $postProcessor;

    /**
     * @var array[]
     */
    private $data;

    /**
     * @param array  $environment   The copy of $_SERVER global variable
     * @param string $configStorage Path to the config files
     */
    public function __construct($environment, $configStorage)
    {
        $this->environment   = $environment;
        $this->configStorage = $configStorage;

        $this->postProcessor = new ConfigPostProcessor();

        $this->postProcessor->addRule(function ($data) {
            if ($data['host_details']['web_dir'] === '/') {
                $data['host_details']['web_dir'] = '';
            }

            return $data;
        });

        $this->postProcessor->addRule(function ($data) {
            $data['host_details']['web_dir_wo_slash'] = rtrim($data['host_details']['web_dir'], '/');

            return $data;
        });

        $this->postProcessor->addRule(function ($data) {
            $domains = empty($data['host_details']['domains']) ? [] : explode(',', $data['host_details']['domains']);

            foreach (['http_host', 'https_host'] as $host) {
                if (!empty($data['host_details']['admin_host'])) {
                    $data['host_details'][$host . '_orig'] = $data['host_details'][$host];
                    $data['host_details'][$host]           = $data['host_details']['admin_host'];
                } elseif ($domains
                    && isset($this->environment['HTTP_HOST'])
                    && $data['host_details'][$host] !== $this->environment['HTTP_HOST']
                    && in_array($this->environment['HTTP_HOST'], $domains, true)
                ) {
                    $data['host_details'][$host . '_orig'] = $data['host_details'][$host];
                    $data['host_details'][$host]           = $this->environment['HTTP_HOST'];
                }
            }

            return $data;
        });
    }

    /**
     * @param string $section
     * @param string $name
     *
     * @return mixed|null
     * @throws ConfigMissingFileException
     * @throws ConfigWrongFormattedFileException
     */
    public function getOption($section, $name)
    {
        if ($this->data === null) {
            $this->loadData();
        }

        return isset($this->data[$section][$name]) ? $this->data[$section][$name] : null;
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
        foreach (array_reverse($this->getFiles()) as $file) {
            try {
                $data = $this->loadFile($file);
            } catch (\Exception $e) {
                $data = [];
            }

            if (isset($data[$section])) {
                $configFile = new ConfigFile($this->configStorage . $file);
                return $configFile->setOption($section, $name, $value);
            }
        }

        return false;
    }

    /**
     * @return array[]
     * @throws ConfigMissingFileException
     * @throws ConfigWrongFormattedFileException
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->loadData();
        }

        return $this->data;
    }

    public function addFile($file)
    {
        $this->files[] = $file;
    }

    /**
     * @return array
     */
    private function getFiles()
    {
        return $this->files;
    }

    /**
     * @throws ConfigMissingFileException
     * @throws ConfigWrongFormattedFileException
     */
    private function loadData()
    {
        $this->data = $this->postProcessor->process($this->readData());
    }

    /**
     * @return array[]
     * @throws ConfigMissingFileException
     * @throws ConfigWrongFormattedFileException
     */
    private function readData()
    {
        $data = $this->loadFile('config.default.php');

        foreach ($this->getFiles() as $file) {
            try {
                $data = array_replace_recursive($data, $this->loadFile($file));
            } catch (ConfigMissingFileException $e) {
            }
        }

        return $data;
    }

    /**
     * @param string $file
     *
     * @return array[]
     * @throws ConfigMissingFileException
     * @throws ConfigWrongFormattedFileException
     */
    private function loadFile($file)
    {
        $configFile = new ConfigFile($this->configStorage . $file);

        return $configFile->getData();
    }
}
