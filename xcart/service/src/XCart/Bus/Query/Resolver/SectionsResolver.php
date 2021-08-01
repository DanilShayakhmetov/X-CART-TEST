<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Silex\Application;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\MarketplaceSectionsDataSource;
use XCart\Marketplace\Constant;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class SectionsResolver
{
    /**
     * @var UrlBuilder
     */
    protected $urlBuilder;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var MarketplaceSectionsDataSource
     */
    protected $marketplaceSectionsDataSource;

    /**
     * @var ModulesResolver
     */
    private $modulesResolver;

    /**
     * @var bool
     */
    private $isCloud;

    /**
     * @param Application                   $app
     * @param UrlBuilder                    $urlBuilder
     * @param Context                       $context
     * @param MarketplaceSectionsDataSource $marketplaceSectionsDataSource
     * @param ModulesResolver               $modulesResolver
     *
     * @return SectionsResolver
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        UrlBuilder $urlBuilder,
        Context $context,
        MarketplaceSectionsDataSource $marketplaceSectionsDataSource,
        ModulesResolver $modulesResolver
    ) {
        return new self(
            $urlBuilder,
            $context,
            $marketplaceSectionsDataSource,
            $modulesResolver,
            $app['xc_config']['service']['is_cloud'] ?? false
        );
    }

    /**
     * @param UrlBuilder                    $urlBuilder
     * @param Context                       $context
     * @param MarketplaceSectionsDataSource $marketplaceSectionsDataSource
     * @param ModulesResolver               $modulesResolver
     * @param boolean                       $isCloud
     */
    public function __construct(
        UrlBuilder $urlBuilder,
        Context $context,
        MarketplaceSectionsDataSource $marketplaceSectionsDataSource,
        ModulesResolver $modulesResolver,
        $isCloud
    ) {
        $this->urlBuilder                    = $urlBuilder;
        $this->context                       = $context;
        $this->marketplaceSectionsDataSource = $marketplaceSectionsDataSource;
        $this->modulesResolver               = $modulesResolver;
        $this->isCloud                       = $isCloud;
    }

    /**
     * Sections resolver
     *
     * @param mixed       $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     * @Resolver()
     */
    public function getList($value, $args, $context, ResolveInfo $info): ?array
    {
        $sections = $this->marketplaceSectionsDataSource->getAll();

        $result = [];
        foreach ($sections as $section) {
            switch ($section[Constant::FIELD_SECTION_TYPE] ?? null) {
                case 'banner':
                    if (!$this->isCloud) {
                        $result[] = $this->fillBannerFields($section);
                    }
                    break;

                case 'addons':
                case 'templates':
                    $result[] = $this->fillModulesFields($section, $context, $info);
                    break;
            }
        }

        return $result ?: null;
    }

    /**
     * @param array $section
     *
     * @return array
     */
    protected function fillBannerFields(array $section): array
    {
        if (!empty($section[Constant::FIELD_SECTION_ADDON])) {
            $url = $this->getUrlByType(Constant::FIELD_SECTION_ADDON, $section[Constant::FIELD_SECTION_ADDON]);
        } elseif (!empty($section[Constant::FIELD_SECTION_TAG])) {
            $url = $this->getUrlByType(Constant::FIELD_SECTION_TAG, $section[Constant::FIELD_SECTION_TAG]);
        } else {
            $url = $section[Constant::FIELD_SECTION_BANNER] ?? null;
        }

        return [
            'id'       => md5($url . $section[Constant::FIELD_SECTION_TYPE] . $section[Constant::FIELD_SECTION_POS]),
            'type'     => $section[Constant::FIELD_SECTION_TYPE],
            'position' => $section[Constant::FIELD_SECTION_POS],
            'url'      => $url,

            'imageURL' => $section[Constant::FIELD_SECTION_IMAGE],
            'content'  => $this->getHtmlContent($section),
        ];
    }

    /**
     * @param array $section
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     */
    protected function fillModulesFields(array $section, $context, ResolveInfo $info): array
    {
        $name = $this->getSectionName($section);
        $url  = $section[Constant::FIELD_SECTION_TYPE] === 'templates'
            ? $this->getTemplateUrl($section[Constant::FIELD_SECTION_TAG])
            : $this->getUrlByType(Constant::FIELD_SECTION_TAG, $section[Constant::FIELD_SECTION_TAG]);

        $modules = array_column($section[Constant::FIELD_SECTION_ADDONS], Constant::FIELD_SECTION_ADDON);

        if ($this->isCloud) {
            $availableModules = $this->modulesResolver->resolvePage(
                [],
                ['includeIds' => $modules, 'onlyAvailable' => true],
                $context,
                $info
            );

            $modules = [];

            foreach ($availableModules['modules'] as $module) {
                $modules[] = $module->id;
            }
        }

        return [
            'id'       => md5($name . $section[Constant::FIELD_SECTION_TYPE] . $section[Constant::FIELD_SECTION_POS]),
            'type'     => $section[Constant::FIELD_SECTION_TYPE],
            'position' => $section[Constant::FIELD_SECTION_POS],
            'url'      => $url,

            'name'    => $name,
            'modules' => $modules,
        ];
    }

    /**
     * Parse and replace links
     *
     * @param array $section
     *
     * @return string
     */
    protected function getHtmlContent(array $section): ?string
    {
        $html = base64_decode($section[Constant::FIELD_SECTION_HTML]);

        if (!empty($section[Constant::FIELD_SECTION_CSS])) {
            $css = base64_decode($section[Constant::FIELD_SECTION_CSS]);

            $html = "<style>$css</style>$html";
        }

        $html = $this->clearUtmSources($html);

        return preg_replace_callback('/\[\[(\w+):([\w\-_ ]+)\]\]/u', [$this, 'replaceLinks'], $html);
    }

    /**
     * @param string $type
     * @param string $value
     *
     * @return string|null
     */
    protected function getUrlByType(string $type, string $value): ?string
    {
        switch ($type) {
            case Constant::FIELD_SECTION_ADDON:
                return '/marketplace?' . http_build_query([
                        'moduleId' => $value,
                    ]);

            case Constant::FIELD_SECTION_TAG:
                return '/available-addons' . ($value ? '?' . http_build_query([
                            'tag' => $value,
                        ]) : '');

            default:
                return null;
        }
    }

    /**
     * @param string $tag
     *
     * @return string
     */
    protected function getTemplateUrl(string $tag = ''): string
    {
        return '/templates' . ($tag ? '?' . http_build_query([
                    'tag' => $tag,
                ]) : '');
    }

    /**
     * @param array $section
     *
     * @return string|null
     */
    protected function getSectionName(array $section): ?string
    {
        return $section[Constant::FIELD_SECTION_TRANSLATIONS][$this->context->languageCode ?? 'en']['section_name']
            ?? $section[Constant::FIELD_NAME]
            ?? null;
    }

    /**
     * Delete all utm_* sources from url
     *
     * @param string $string
     *
     * @return string
     */
    protected function clearUtmSources(string $string): string
    {
        return preg_replace('/([\?\&]utm_[\w]*=[\w-]*)/', '', $string);
    }

    /**
     * @param array $matches
     *
     * @return string|null
     */
    private function replaceLinks(array $matches): ?string
    {
        [$original, $type, $link] = $matches;

        return $this->urlBuilder->buildServiceUrl(
            ltrim($this->getUrlByType($type, $link) ?? $original, '/')
        );
    }
}