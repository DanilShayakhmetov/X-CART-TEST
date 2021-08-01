<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild\Executor\Entry;

use Includes\Utils\Module\Module;
use Includes\Utils\ModulesManager;

class UpgradeAction
{
    /**
     * @var array
     */
    private $state;

    /**
     * @var bool
     */
    private $finished;

    /**
     * @param array $state
     */
    public function __construct($state)
    {
        $this->state = is_array($state) ? $state : [];
    }

    /**
     * @throws \Exception
     */
    public function process()
    {
        if ($this->finished) {
            return;
        }

        // Set internal flag
        if (!defined('LC_CACHE_BUILDING')) {
            define('LC_CACHE_BUILDING', true);
        }

        if (!defined('LC_USE_CLEAN_URLS')) {
            define('LC_USE_CLEAN_URLS', false);
        }

        $arguments = $this->extractArgFromState($this->state);

        $this->addLabels($arguments['moduleId']);

        $this->finished = true;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * @param array $state
     *
     * @return mixed
     */
    protected function extractArgFromState($state): ?array
    {
        return isset($state['arg'])
            ? $state['arg']
            : null;
    }

    /**
     * @param string $moduleId
     */
    protected function addLabels($moduleId)
    {
        [$author, $name] = Module::explodeModuleId($moduleId);

        $yamlFiles = $moduleId === 'CDev-Core'
            ? ModulesManager::getCoreYAMLFiles()
            : ModulesManager::getModuleYAMLFiles($author, $name);

        if ($yamlFiles) {
            // Load data from yaml files
            foreach ($yamlFiles as $yamlFile) {
                $areLabelsLoaded = \XLite\Core\Translation::getInstance()->loadLabelsFromYaml($yamlFile);
            }

            // Reset cache of language translations
            \XLite\Core\Translation::getInstance()->reset();
        }
    }
}
