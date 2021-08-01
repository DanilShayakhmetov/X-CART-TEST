<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model\Repo;

/**
 * Clean URL repository
 */
 class CleanURL extends \XLite\Module\XC\News\Model\Repo\CleanURL implements \XLite\Base\IDecorator
{
    const STATIC_PAGE_URL_FORMAT_NO_EXT = 'domain/goalpage';
    const STATIC_PAGE_URL_FORMAT_EXT = 'domain/goalpage.html';

    /**
     * Returns 'product_clean_urls_format' option value
     *
     * @return string
     */
    public static function getStaticPageCleanUrlFormat()
    {
        $format = \Includes\Utils\ConfigParser::getOptions(array('clean_urls', 'static_page_clean_urls_format'));

        return in_array($format, [
            static::STATIC_PAGE_URL_FORMAT_EXT,
            static::STATIC_PAGE_URL_FORMAT_NO_EXT
        ])
            ? $format
            : static::STATIC_PAGE_URL_FORMAT_NO_EXT;
    }

    /**
     * Is use extension for categories
     *
     * @return boolean
     */
    public static function isStaticPageUrlHasExt()
    {
        return static::getStaticPageCleanUrlFormat() === static::STATIC_PAGE_URL_FORMAT_EXT;
    }

    /**
     * Returns available entities types
     *
     * @return array
     */
    public static function getEntityTypes()
    {
        $list = parent::getEntityTypes();
        $list['XLite\Module\CDev\SimpleCMS\Model\Page'] = 'page';

        return $list;
    }

    /**
     * Post process clean URL
     *
     * @param string $url URL
     * @param \XLite\Model\Base\Catalog $entity Entity
     *
     * @return string
     */
    protected function postProcessURLPage($url, $entity, $ignoreExtension = false)
    {
        return $url . ($this->isStaticPageUrlHasExt() && !$ignoreExtension ? '.' . static::CLEAN_URL_DEFAULT_EXTENSION : '');
    }

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLPage($url, $last = '', $rest = '', $ext = '')
    {
        $result = $this->findByURL('page', $url . $ext);

        return $result;
    }

    /**
     * Hook for modules
     *
     * @param string $url Main part of a clean URL
     * @param string $last First part before the "url"
     * @param string $rest Part before the "url" and "last"
     * @param string $ext Extension
     * @param string $target Target
     * @param array $params Additional params
     *
     * @return array
     */
    protected function prepareParseURL($url, $last, $rest, $ext, $target, $params)
    {
        list($target, $params) = parent::prepareParseURL($url, $last, $rest, $ext, $target, $params);

        if ('page' == $target && !empty($last)) {
            unset($params['id']);
        }

        return [$target, $params];
    }

    /**
     * Build product URL
     *
     * @param array $params Params
     *
     * @return array
     */
    protected function buildURLPage($params)
    {
        $urlParts = [];

        if (!empty($params['id'])) {
            /** @var \XLite\Module\CDev\SimpleCMS\Model\Page $page */
            $page = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')->find($params['id']);

            if (isset($page) && $page->getCleanURL()) {
                $urlParts[] = $page->getCleanURL();
                unset($params['id']);
            }
        }

        return [$urlParts, $params];
    }

    /**
     * Build fake url with placeholder
     *
     * @param \XLite\Model\AEntity|string $entity Entity
     * @param array $params Params
     * @param boolean                     $ignoreExtension Ignore default extension
     *
     * @return array
     */
    protected function buildFakeURLPage($entity, $params, $ignoreExtension = false)
    {
        $urlParts = [$this->postProcessURL(static::PLACEHOLDER, $entity, $ignoreExtension)];

        return [$urlParts, $params];
    }

    /**
     * @param string $cleanURL
     * @return \XLite\Model\Base\Catalog
     */
    protected function findCategoryConflictWithOtherTypes($cleanURL)
    {
        return parent::findCategoryConflictWithOtherTypes($cleanURL) ?: $this->findEntityByURL('page', $cleanURL);
    }
}
