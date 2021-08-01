<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\RSS;

/**
 * RSS
 */
class RSS extends \XLite\View\Dialog
{
    /**
     * Max count of feeds
     */
    const MAX_COUNT  = 1;

    /**
     * Feeds
     *
     * @var array
     */
    protected $feeds;

    /**
     * Add widget specific CSS file
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache TTL (seconds)
     *
     * @return integer
     */
    protected function getCacheTTL()
    {
        return 1800;
    }

    /**
     * Return widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'rss';
    }

    /**
     * Return RSS feed Url
     *
     * @return string
     */
    protected function getRSSFeedUrl()
    {
        return 'http://feeds.feedburner.com/qtmsoft';
    }

    /**
     * Return RSS Url
     *
     * @return string
     */
    protected function getRSSUrl()
    {
        return \XLite::getInstallationLng() === 'ru'
            ? 'https://www.x-cart.ru/rss_x_cart_5.xml'
            : 'https://www.x-cart.com/rss_x_cart_5.xml';
    }

    /**
     * Return Blog Url
     *
     * @return string
     */
    protected function getBlogUrl()
    {
        return \XLite::getInstallationLng() === 'ru'
            ? 'https://www.x-cart.ru/blog'
            : 'https://blog.x-cart.com';
    }

    /**
     * Prepare feeds
     *
     * @param string $url Url
     *
     * @return array
     */
    protected function prepareFeeds($url)
    {
        $feed = simplexml_load_string(
            $this->getContentByUrl($url)
        );

        $result = array();
        if ($feed && $feed->channel->item) {
            foreach ($feed->channel->item as $story) {
                $params = [
                    'utm_source'    => 'xc5admin',
                    'utm_medium'    => 'link2blog',
                    'utm_campaign'  => 'xc5adminlink2blog'
                ];
                $link = \XLite\Core\URLManager::appendParamsToUrl($story->link, $params);
                $result[] = array (
                    'title' => (string) $story->title,
                    'desc'  => (string) $story->description,
                    'link'  => $link,
                    'date'  => strtotime($story->pubDate),
                );

                if (static::MAX_COUNT <= count($result)) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function getContentByUrl($url)
    {
        $request = new \XLite\Core\HTTP\Request($url);
        $response = $request->sendRequest();

        return $response->body;
    }

    /**
     * Return feeds
     *
     * @return array
     */
    protected function getFeeds()
    {
        if (!isset($this->feeds)) {
            $this->feeds = $this->prepareFeeds(
                $this->getRSSUrl()
            );
        }

        return $this->feeds;
    }
}
