<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain;

/**
 * from main.yaml:
 * @property string   $id
 * @property string   $version
 * @property string   $type
 * @property string   $author
 * @property string   $name
 * @property string   $authorName
 * @property string   $moduleName
 * @property string   $description
 * @property string   $minorRequiredCoreVersion
 * @property string[] $dependsOn
 * @property string[] $incompatibleWith
 * @property string[] $skins
 * @property bool     $showSettingsForm
 * @property bool     $isSystem
 * @property bool     $canDisable
 *
 * fom package metadata
 * @property string   $icon
 *
 * from xcart module
 * @property bool     $installed
 * @property int      $installedDate
 * @property bool     $integrated
 * @property bool     $enabled
 * @property int      $enabledDate
 * @property string   $skinPreview
 * @property array    $service
 *
 * from marketplace:
 * @property string   $pageURL
 * @property string   $authorPageUrl
 * @property string   $authorEmail
 * @property int      $revisionDate
 * @property float    $price
 * @property float    $origPrice
 * @property string   $currency
 * @property int      $downloads
 * @property int      $rating
 * @property int      $votes
 * @property string[] $tags
 * @property string[] $translations
 * @property string[] $editions
 * @property string   $editionState
 * @property bool     $hasLicense
 * @property string   $xcnPlan
 * @property string   $wave
 * @property bool     $private
 * @property string   $xbProductId
 * @property int      $packSize
 * @property string   $salesChannelPos
 * @property bool     $isLanding
 * @property int      $landingPosition
 *
 * temporary:
 * @property string[] $actions
 * @property array    $scenarioState
 * @property bool     $custom
 * @property string   $purchaseUrl
 * @property string   $hash
 * @property string   $installedVersion
 * @property array    $requiredBy
 * @property mixed    $license
 * @property array    $messages
 * @property string   $edition    From license
 * @property int      $expiration License expiration
 * @property bool     $onSale
 * @property bool     $uploaded
 *
 * @property array    $integrityViolations
 * @property array    $integrityViolationsCache
 * @property string   $changelog
 */
class Module extends PropertyBag
{
    /**
     * @param array $data data to set
     */
    public function __construct(array $data = null)
    {
        $common = [];

        if (isset($data['dependsOn'])) {
            $data['dependsOn'] = array_map(function ($item) {
                if (is_string($item)) {
                    return static::convertModuleId($item);
                }

                $item['id'] = static::convertModuleId($item['id']);

                return $item;
            }, $data['dependsOn']);
        }

        if (isset($data['incompatibleWith'])) {
            $data['incompatibleWith'] = array_map(function ($item) {
                if (is_string($item)) {
                    return static::convertModuleId($item);
                }

                $item['id'] = static::convertModuleId($item['id']);

                return $item;
            }, $data['incompatibleWith']);
        }

        parent::__construct(array_replace($common, $data ?? []));
    }

    /**
     * @param string $version
     *
     * @return self
     */
    public static function generateCoreModule(string $version): self
    {
        return new self([
            'version'                  => $version,
            'id'                       => 'CDev-Core',
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
            'installedDate'            => time(),
            'integrated'               => true,
            'enabled'                  => true,
        ]);
    }

    /**
     * @param string $version
     *
     * @return self
     */
    public static function generateServiceModule(string $version): self
    {
        return new self([
            'version'                  => $version,
            'id'                       => 'XC-Service',
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
        ]);
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    public static function explodeModuleId(string $moduleId): array
    {
        return preg_split('/\\\\|-/', $moduleId);
    }

    /**
     * @param string $moduleId
     *
     * @return string
     */
    public static function convertModuleId(string $moduleId): string
    {
        return str_replace('\\', '-', $moduleId);
    }

    /**
     * @param string $moduleId
     *
     * @return string
     */
    public static function convertModuleIdXCart(string $moduleId): string
    {
        return str_replace('-', '\\', $moduleId);
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function buildModuleId(string $author, ?string $name = null): string
    {
        if ($name === null) {
            [$author, $name] = self::explodeModuleId($author);
        }

        return $author . '-' . $name;
    }

    /**
     * @param string $version
     *
     * @return int[]
     */
    public static function explodeVersion(?string $version = ''): array
    {
        return array_map('intval', array_pad(explode('.', $version ?: ''), 4, 0));
    }

    /**
     * @param array $metadata
     *
     * @return self
     */
    public static function fromPackageMetadata(array $metadata): self
    {
        $module = new self;

        [$author, $name] = self::explodeModuleId($metadata['ActualName']);

        $module->id = self::convertModuleId($metadata['ActualName']);

        $module->version = implode('.', [
            $metadata['VersionMajor'],
            $metadata['VersionMinor'],
            $metadata['VersionBuild'],
        ]);

        $module->author                   = $author;
        $module->name                     = $name;
        $module->authorName               = $metadata['Author'];
        $module->moduleName               = $metadata['Name'];
        $module->description              = $metadata['Description'];
        $module->minorRequiredCoreVersion = $metadata['MinCoreVersion'];

        $module->dependsOn = array_map(static function ($moduleId) {
            return Module::convertModuleId($moduleId);
        }, $metadata['Dependencies'] ?: []);

        $module->isSystem = $metadata['isSystem'];

        $module->icon = $metadata['IconLink'];

        return $module;
    }

    /**
     * @param $version
     * @param $comareVersion
     *
     * @return bool
     */
    public static function isPreviuosMajorVersion($version, $comareVersion): bool
    {
        [$system, $major, ,] = self::explodeVersion($version);
        [$compareSystem, $compareMajor, ,] = self::explodeVersion($comareVersion);

        return $system === $compareSystem && $compareMajor - $major === 1;
    }

    /**
     * @return array
     */
    public function toPackageMetadata(): array
    {
        [$system, $major, $minor, $build] = self::explodeVersion($this->version ?? '');

        return [
            'RevisionDate'   => time(),
            'ActualName'     => self::convertModuleIdXCart($this->id),
            'VersionMinor'   => $minor,
            'VersionMajor'   => $system . '.' . $major,
            'VersionBuild'   => $build,
            'MinCoreVersion' => $this->minorRequiredCoreVersion,
            'Name'           => $this->moduleName,
            'Author'         => $this->authorName,
            'IconLink'       => $this->icon,
            'Description'    => $this->description,

            'Dependencies' => array_map(static function ($moduleId) {
                return Module::convertModuleIdXCart($moduleId);
            }, $this->dependsOn ?? []),

            'isSystem' => $this->isSystem,
        ];
    }
}
