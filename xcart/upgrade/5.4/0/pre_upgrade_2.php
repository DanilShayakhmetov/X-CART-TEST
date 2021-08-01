<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * 5.3.x API!
 */

namespace PreUpgradeHook {

    /**
     * @param string $file
     * @param mixed  $data
     *
     * @return bool
     */
    function saveData($file, $data)
    {
        return \Includes\Utils\FileManager::write(LC_DIR_SERVICE . $file, serialize($data));
    }

    function convertLicenseInfo()
    {
        $data = [];

        /** @var \XLite\Model\ModuleKey[] $licenses */
        $licenses = \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findAll();
        foreach ($licenses as $license) {
            // convert
            $id        = $license->getAuthor() . '-' . $license->getName() . '-' . $license->getKeyType();
            $data[$id] = [
                'name'     => $license->getName(),
                'author'   => $license->getAuthor(),
                'keyType'  => $license->getKeyType(),
                'xcnPlan'  => $license->getXcnPlan(),

                /*
                 * editionName
                 * expDate
                 * prolongKey
                 * wave
                 * xbProductId
                 */
                'keyData'  => $license->getKeyData(),
                // 'key'      => '', // unused
                'active'   => (bool) $license->getActive(),
                'keyValue' => $license->getKeyValue(),
                'id'       => $id,
            ];
        }

        \PreUpgradeHook\saveData('licenseStorage.data', $data);
    }

    function convertInstalledModules()
    {
        $data = array_merge([], \PreUpgradeHook\getCoreModule(), \PreUpgradeHook\getServiceModule());

        /** @var \XLite\Model\Module[] $modules */
        $modules = \XLite\Core\Database::getRepo('XLite\Model\Module')->findBy(['installed' => true]);

        $types = [
            0x1  => 'custom',
            0x2  => 'payment',
            0x4  => 'skin',
            0x8  => 'shipping',
            null => 'common',
        ];

        foreach ($modules as $module) {
            $version = $module->getMajorVersion() . '.' . $module->getMinorVersion() . '.' . ($module->getBuild() ?: '0');
            $id      = $module->getAuthor() . '-' . $module->getName();

            $typeCode = $module->callModuleMethod('getModuleType');

            $data[$id] = [
                'version'                  => $version,
                'id'                       => $id,
                'type'                     => $types[$typeCode],
                'author'                   => $module->getAuthor(),
                'name'                     => $module->getName(),
                'authorName'               => $module->getAuthorName(),
                'moduleName'               => $module->getModuleName(),
                'description'              => $module->getDescription(),
                'minorRequiredCoreVersion' => $module->getMinorRequiredCoreVersion(),
                'dependsOn'                => $module->getDependencies(),
                'incompatibleWith'         => $module->callModuleMethod('getMutualModulesList', []),
                'showSettingsForm'         => $module->callModuleMethod('showSettingsForm', false),
                'isSystem'                 => $module->getIsSystem(),
                'canDisable'               => $module->canDisable(),
                'icon'                     => '', // $module->getIconURL(),//   'skins/admin/images/core_image.png',
                'installed'                => $module->getInstalled(),
                'installedDate'            => $module->getDate() ?: \XLite\Core\Config::getInstance()->Version->timestamp,
                'integrated'               => $module->getYamlLoaded(),
                'enabled'                  => $module->getEnabled(),
            ];
        }

        \PreUpgradeHook\saveData('tmp.busInstalledModulesStorage.data', $data);
    }

    function getCoreModule()
    {
        $id = 'CDev-Core';

        return [
            $id => [
                'version'                  => \XLite::XC_VERSION,
                'id'                       => $id,
                'type'                     => 'core',
                'author'                   => 'CDev',
                'name'                     => 'Core',
                'authorName'               => 'X-Cart team',
                'moduleName'               => 'Core',
                'description'              => '',
                'minorRequiredCoreVersion' => '',
                'dependsOn'                => [],
                'incompatibleWith'         => [],
                'showSettingsForm'         => false,
                'isSystem'                 => true,
                'canDisable'               => false,
                'icon'                     => 'skins/admin/images/core_image.png',
                'installed'                => true,
                'installedDate'            => \XLite\Core\Config::getInstance()->Version->timestamp,
                'integrated'               => true,
                'enabled'                  => true,
            ],
        ];
    }

    function getServiceModule()
    {
        $id = 'XC-Service';

        return [
            $id => [
                'version'                  => '5.3.0.0',
                'id'                       => $id,
                'type'                     => 'core',
                'author'                   => 'XC',
                'name'                     => 'Service',
                'authorName'               => 'X-Cart team',
                'moduleName'               => 'Service',
                'description'              => '',
                'minorRequiredCoreVersion' => '',
                'dependsOn'                => [],
                'incompatibleWith'         => [],
                'showSettingsForm'         => false,
                'isSystem'                 => true,
                'canDisable'               => false,
                'icon'                     => 'skins/admin/images/core_image.png',
                'installed'                => true,
                'installedDate'            => time(),
                'integrated'               => true,
                'enabled'                  => true,
            ],
        ];
    }

    function convertCoreConfig()
    {
        $data = [
            'version' => \XLite::XC_VERSION,
            'wave'    => \XLite\Core\Config::getInstance()->Environment->upgrade_wave,
        ];

        \PreUpgradeHook\saveData('coreConfigStorage.data', $data);
    }
}

namespace {

    return function () {
        \PreUpgradeHook\convertLicenseInfo();
        \PreUpgradeHook\convertInstalledModules();
        \PreUpgradeHook\convertCoreConfig();
    };
}
