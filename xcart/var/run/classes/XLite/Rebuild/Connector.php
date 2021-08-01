<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Rebuild;

use Includes\Utils\Module\Manager;
use XLite\Core\Skin;

class Connector
{
    /**
     * @param array $params
     * @return string
     */
    public static function getFrontendUrl(array $params = [])
    {
        return \XLite::getInstance()->getServiceURL('#/', null, $params);
    }

    /**
     * @param string $path
     * @param $params
     * @return string
     */
    public static function getBackendUrl($path = '', array $params = [])
    {
        return \XLite::getInstance()->getShopURL('service.php?' . $path, null, $params);
    }

    /**
     * @param string $id Skin module id
     * @param string $returnUrl
     *
     * @return bool
     */
    public static function enableSkin($id, $returnUrl = '')
    {
        $scenario = \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
            \XLite\Core\Marketplace\QueryRegistry::getMutation('changeSkinState', [
                'moduleId' => $id,
                'returnUrl' => $returnUrl
            ]),
            new \XLite\Core\Marketplace\Normalizer\Raw()
        ) ?: [];

        $currentSkinId    = Skin::getInstance()->getCurrentSkinModuleId();
        $isRequired       = false;

        $required_modules = array_filter(
            Manager::getRegistry()->getModules(),
            static function ($module) use ($currentSkinId) {
                /** @var \Includes\Utils\Module\Module $module */
                if ($module->enabled) {
                    foreach ((array) $module->dependsOn as $dependency) {
                        if (
                            (is_array($dependency) && $dependency['id'] === $currentSkinId)
                            || (is_string($dependency) && str_replace('\\', '-', $dependency) === $currentSkinId)
                        ) {
                            return true;
                        }
                    }
                }

                return false;
            }
        );

        foreach ($required_modules as $module) {
            \XLite\Core\TopMessage::addError('Cannot be disabled. The template is required by: {{moduleName}}', ['moduleName' => '<a href="' . \XLite::getInstance()->getServiceURL('#/installed-addons', null, ['moduleId' => $module->id]) . '" target="_blank">' . $module->id . '</a>']);
            $isRequired = true;
        }

        if ($isRequired) {
            return false;
        }

        if (!empty($scenario['changeSkinState']['id'])) {
            $rebuildState = \XLite\Core\Marketplace\Retriever::getInstance()->retrieve(
                \XLite\Core\Marketplace\QueryRegistry::getMutation('startRebuild', [
                    'id' => $scenario['changeSkinState']['id']
                ]),
                new \XLite\Core\Marketplace\Normalizer\Raw()
            ) ?: [];

            if (!empty($rebuildState['startRebuild']['id'])) {
                \XLite::getController()->setReturnURL(
                    self::getBackendUrl('#/rebuild/' .  $rebuildState['startRebuild']['id'])
                );

                return true;
            }
        }

        \XLite\Core\TopMessage::addError(\XLite\Core\Translation::lbl('Unable to connect to maintenance system'));
    }
}