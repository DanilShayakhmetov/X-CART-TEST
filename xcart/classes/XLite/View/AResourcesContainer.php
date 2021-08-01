<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\ConfigParser;

/**
 * Resources container routine
 */
abstract class AResourcesContainer extends \XLite\View\Container
{
    /**
     * Optimized resources
     *
     * @var array
     */
    protected static $optimizedResources = [];

    /**
     * Latest cache timestamp
     *
     * @var   integer
     */
    public static $latestCacheTimestamp;

    /**
     * Return cache dir path for resources
     *
     * @param array $params
     *
     * @return string
     */
    public static function getResourceCacheDir(array $params)
    {
        return LC_DIR_CACHE_RESOURCES . implode(LC_DS, $params) . LC_DS;
    }

    /**
     * Return minified resources cache dir
     *
     * @param string $type Resource type, either 'js' or 'css'
     *
     * @return string
     */
    public static function getMinifiedCacheDir($type)
    {
        return LC_DIR_CACHE_RESOURCES . $type . LC_DS;
    }

    /**
     * Return CSS resources structure from the file cache
     *
     * @param array $resources
     *
     * @return array
     */
    public function getCSSResourceFromCache(array $resources)
    {
        return $this->getResourceFromCache(
                static::RESOURCE_CSS,
                $resources,
                [
                    static::RESOURCE_CSS,
                    \XLite\Core\Request::getInstance()->isHTTPS() ? 'https' : 'http',
                    $resources[0]['media'],
                ],
                'prepareCSSCache'
            ) + ['media' => $resources[0]['media']];
    }

    /**
     * Return latest time stamp of cache build procedure
     *
     * @return integer
     */
    public static function getLatestCacheTimestamp()
    {
        if (!isset(\XLite\View\AResourcesContainer::$latestCacheTimestamp)) {
            \XLite\View\AResourcesContainer::$latestCacheTimestamp = intval(
                \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(\XLite::CACHE_TIMESTAMP)
            );
        }

        return \XLite\View\AResourcesContainer::$latestCacheTimestamp;
    }

    /**
     * Return JS resources structure from the file cache
     *
     * @param array $resources
     *
     * @return array
     */
    protected function getJSResourceFromCache(array $resources)
    {
        return $this->getResourceFromCache(
            static::RESOURCE_JS,
            $resources,
            [static::RESOURCE_JS],
            'prepareJSCache'
        );
    }

    /**
     * Return resource structure from the file cache
     *
     * @param string $type                   File type of resource (js/css)
     * @param array  $resources              Resources for caching
     * @param array  $paramsForCache         Parameters of file cache (directory structure path to
     *                                       file)
     * @param string $prepareCacheFileMethod Method of $this object to read one resource entity and
     *                                       do some inner work if it is necessary
     *
     * @return array
     */
    protected function getResourceFromCache($type, array $resources, array $paramsForCache, $prepareCacheFileMethod)
    {
        $pathToCacheDir = static::getResourceCacheDir($paramsForCache);
        \Includes\Utils\FileManager::mkdirRecursive($pathToCacheDir);

        $file = hash('sha256', serialize($resources)) . '.' . $type;
        $filePath = $pathToCacheDir . $file;

        if (!\Includes\Utils\FileManager::isFile($filePath)) {
            $content = '';
            foreach ($resources as $resource) {
                $content .= $this->$prepareCacheFileMethod(
                    $resource,
                    $pathToCacheDir
                );
            }
            \Includes\Utils\FileManager::write($filePath, $content);
        }

        return [
            'file' => $filePath,
            'url'  => \XLite::getInstance()
                ->getShopURL(
                    str_replace(LC_DS, '/', substr($filePath, strlen(LC_DIR_ROOT))),
                    \XLite\Core\Request::getInstance()->isHTTPS()
                ),
        ];
    }

    /**
     * Prepares CSS cache to use. Main issue - replace url($resourcePath) construction with
     * url($aggregatedResourcePath) or url($minifiedResourcePath)
     *
     * @param array $resource Array with CSS file data
     *
     * @return string
     */
    protected function prepareCSSCache($resource, $dir)
    {
        $data = '';
        if (isset($resource['file'])) {
            $filePath = $resource['file'];
            $minFilePath = str_replace(LC_DIR_SKINS, static::getMinifiedCacheDir(static::RESOURCE_CSS), $filePath);
            $minFilePath = dirname($minFilePath) . LC_DS . basename($minFilePath, '.css') . '.min.css';
            $minified = false;

            $origPath = $filePath;

            if (\Includes\Utils\FileManager::isFileReadable($minFilePath)) {
                $data = \Includes\Utils\FileManager::read($minFilePath);
                $minified = true;
                $origPath = $minFilePath;
            } else {
                $data = \Includes\Utils\FileManager::read($filePath);
            }

            $noMinify = !empty($resource['no_minify']) || !empty($resource['no-minify']);

            if (!$minified && !$noMinify && strpos(basename($filePath), '.min.css') == false) {
                $data = preg_replace_callback(
                    '/url\(([^)]+)\)/Ss',
                    function (array $matches) use ($minFilePath, $filePath) {
                        return $this->processCSSURLHandler($matches, $minFilePath, $filePath);
                    },
                    $data
                );

                // Minify CSS content
                $data = $this->minifyCSS($data, $filePath);

                \Includes\Utils\FileManager::write($minFilePath, $data);

                $origPath = $minFilePath;
            }

            $data = preg_replace_callback(
                '/url\(([^)]+)\)/Ss',
                function (array $matches) use ($dir, $origPath) {
                    return $this->processCSSURLHandler($matches, $dir, $origPath);
                },
                $data
            );

            $data = trim($data);
        }

        return $data
            ? PHP_EOL . '/* AUTOGENERATED: ' . basename($filePath) . ' */' . PHP_EOL . $data
            : '';
    }

    /**
     * Get minified CSS content
     *
     * @param string $content  Source CSS content
     * @param string $filePath Source file path
     *
     * @return string
     */
    protected function minifyCSS($content, $filePath)
    {
        $minifier = new \tubalmartin\CssMin\Minifier();

        return $minifier->run($content);
    }

    /**
     * Process CSS URL callback
     *
     * @param array  $matches  Matches
     * @param string $filePath File prefix
     * @param string $origPath
     *
     * @return string
     */
    public function processCSSURLHandler(array $matches, $filePath, $origPath)
    {
        $url           = trim($matches[1]);
        $isDataURL = true;

        if (!preg_match('/^[\'"]?data:/Ss', $url)) {
            $isDataURL = false;
            $first         = substr($url, 0, 1);

            if ('"' == $first || '\'' == $first) {
                $url = stripslashes(substr($url, 1, -1));
            }

            if (!preg_match('/^(?:https?:)?\/\//Ss', $url)) {
                if ('/' === substr($url, 0, 1)) {
                    $dir = LC_DIR_ROOT;
                } else {
                    $dir = dirname($origPath) . LC_DS;
                }

                $url = str_replace(LC_DS, '/', \Includes\Utils\FileManager::makeRelativePath(
                    $filePath,
                    $dir . str_replace('/', LC_DS, $url)
                ));

                $url = $this->fixUrl($url);
            }

            if (preg_match('/[\'"]/Ss', $url)) {
                $url = '"' . addslashes($url) . '"';
            }
        }

        return $isDataURL
            ? 'url(' . $url . ')'
            : 'url("' . $url . '")';
    }

    protected function fixUrl($url)
    {
        $fixed = preg_replace('#[^/.]++/(\.\.)++/?#', '', $url);

        return $fixed === $url
            ? $fixed
            : $this->fixUrl($fixed);
    }

    /**
     * Prepares JS cache to use
     *
     * @param array $resource Array with JS file data
     *
     * @return string
     */
    protected function prepareJSCache($resource)
    {
        $data = '';
        if (isset($resource['file'])) {
            $filePath = $resource['file'];
            $minFilePath = str_replace(LC_DIR_SKINS, static::getMinifiedCacheDir(static::RESOURCE_JS), $filePath);
            $minFilePath = dirname($minFilePath) . LC_DS . basename($minFilePath, '.js') . '.min.js';
            $minified = false;

            // Get file content
            if (\Includes\Utils\FileManager::isFileReadable($minFilePath)) {
                $data = \Includes\Utils\FileManager::read($minFilePath);
                $minified = true;
            } else {
                $data = \Includes\Utils\FileManager::read($filePath);
            }

            $noMinify = !empty($resource['no_minify']) || !empty($resource['no-minify']);

            if (!$minified && !$noMinify && strpos(basename($filePath), '.min.js') == false) {
                // Minify js content
                $data = $this->minifyJS($data, $filePath);

                \Includes\Utils\FileManager::write($minFilePath, $data);
            }

            $data = trim($data);

            $data = preg_replace('/\)$/S', ');', $data);
        }

        return $data
            ? PHP_EOL . '/* AUTOGENERATED: ' . basename($filePath) . ' */' . PHP_EOL . $data . ';'
            : '';
    }

    /**
     * Make simple js-minification and return minified JS content
     *
     * @param string $content  Source JS content
     * @param string $filePath Source file path
     *
     * @return string
     */
    protected function minifyJS($content, $filePath)
    {
        require_once LC_DIR_LIB . 'Minify' . LC_DS . 'JSMinPlus.php';

        try {
            ob_start();
            $result = \JSMinPlus::minify($content);
            $error = ob_get_contents();
            ob_end_clean();

            if (false === $result) {
                throw new \Exception(sprintf('[%s] %s', $filePath, $error));
            }

        } catch (\Exception $e) {
            //\XLite\Logger::getInstance()->registerException($e);
            $result = $content;
        }

        return $result;
    }

    /**
     * Check if the CSS resources should be aggregated
     *
     * @return boolean
     */
    protected function doCSSAggregation()
    {
        return \XLite\Core\Config::getInstance()->Performance->aggregate_css;
    }

    /**
     * Check if the CSS resources should be SEO-optimized
     *
     * @return boolean
     */
    protected function doCSSOptimization()
    {
        return $this->doCSSAggregation()
            && \Includes\Utils\ConfigParser::getOptions(['storefront_options', 'optimize_css'])
            && !\XLite\Core\Request::getInstance()->isIE(); // Disable CSS optimization for IE, BUG-6818
    }

    /**
     * Check if the JS resources should be aggregated
     *
     * @return boolean
     */
    protected function doJSAggregation()
    {
        return \XLite\Core\Config::getInstance()->Performance->aggregate_js;
    }

    /**
     * Add specific unique identificator to resource URL
     *
     * @param string $url
     *
     * @return string
     */
    protected function getResourceURL($url, $params = [])
    {
        return isset($params['no_timestamp']) && $params['no_timestamp']
            ? $url
            : $url . (strpos($url, '?') === false ? '?' : '&') . static::getLatestCacheTimestamp();
    }

    /**
     * Get collected javascript resources
     *
     * @return array
     */
    protected function getJSResources()
    {
        return \XLite\Core\Layout::getInstance()->getPreparedResourcesByType(static::RESOURCE_JS);
    }

    /**
     * Get collected CSS resources
     *
     * @return array
     */
    protected function getCSSResources()
    {
        return \XLite\Core\Layout::getInstance()->getPreparedResourcesByType(static::RESOURCE_CSS);
    }

    /**
     * Resources must be grouped if the outer CSS or JS resource is used
     * For example:
     * array(
     *      controller.js,
     *      button.js,
     *      http://google.com/script.js,
     *      tail.js
     * )
     *
     * is grouped into:
     *
     * array(
     *      array(
     *          controller.js,
     *          button.js,
     *      ),
     *      array(http://google.com/script.js),
     *      array(
     *          tail.js
     *      )
     * )
     *
     * Then the local resources are cached according $cacheHandler method.
     *
     * @param array  $resources    Resources array
     * @param atring $cacheHandler Cache handler method
     *
     * @return array
     */
    public function groupResourcesByUrl($resources, $cacheHandler)
    {
        $groupByUrl = [];
        $group = [];

        foreach ($resources as $info) {
            if (0 === strpos($info['url'], '//')) {
                $info['url'] = (\XLite\Core\Request::getInstance()->isHTTPS() ? 'https:' : 'http:') . $info['url'];
            }

            $urlData = parse_url($info['url']);

            if (isset($urlData['host']) && !isset($info['file'])) {
                $groupByUrl = array_merge(
                    $groupByUrl,
                    empty($group) ? [] : [$this->$cacheHandler($group)],
                    [$info]
                );

                $group = [];
            } else {
                $group[] = $info;
            }
        }

        return array_merge($groupByUrl, empty($group) ? [] : [$this->$cacheHandler($group)]);
    }

    /**
     * Get collected JS resources
     *
     * @return array
     */
    protected function getAggregateJSResources()
    {
        return $this->groupResourcesByUrl($this->getJSResources(), 'getJSResourceFromCache');
    }

    /**
     * Get collected CSS resources
     *
     * @return array
     */
    protected function getAggregateCSSResources()
    {
        $list = $this->getCSSResources();

        // Group CSS resources by media type
        $groupByMedia = [];

        foreach ($list as $fileInfo) {

            $index = (isset($fileInfo['interface']) && 'common' == $fileInfo['interface'] ? 'common-' : '')
                . (isset($fileInfo['media']) ? $fileInfo['media'] : 'all');

            $groupByMedia[$index][] = $fileInfo;
        }

        $list = [];
        foreach ($groupByMedia as $group) {
            $list = array_merge($list, $this->groupResourcesByUrl($group, 'getCSSResourceFromCache'));
        }

        return $list;
    }

    /**
     * Return style tag with content
     *
     * @param array $resource
     *
     * @return string
     */
    protected function getInternalCssByResource($resource)
    {
        if (!isset($resource['file'])) {
            return '';
        }

        $filePath = $resource['file'];

        $content = file_get_contents($filePath);

        if (isset($resource['media'])) {
            switch ($resource['media']) {
                case 'print':
                    return '';
                case 'all':
                    break;
                default:
                    $content = "@media {$resource['media']} {" . $content . '}';
            }
        }

        $webDir = ConfigParser::getOptions(['host_details', 'web_dir_wo_slash']);

        $content = preg_replace_callback(
            '/url\(([^)]+)\)/Ss',
            function (array $matches) use ($filePath, $webDir) {
                $relativeUrl = $this->processCSSURLHandler($matches, LC_DIR_ROOT, $filePath);

                if (
                    !preg_match('/^[\'"]?data:/Ss', $matches[1])
                    && !preg_match('/^(?:https?:)?\/\//Ss', $matches[1])
                ) {
                    $prefix = $webDir ? $webDir . '/' : '';
                    return 'url("' . $prefix . mb_substr($relativeUrl, 5);
                }

                return $relativeUrl;
            },
            $content
        );

        $content = '<style>' . $content . '</style>';

        return $content;
    }

    /**
     * Check if we need to "optimize" resource
     *
     * @param $resource
     *
     * @return bool
     */
    protected function isResourceSuitableForOptimization($resource)
    {
        if (isset($resource['file'])) {
            $resourceKey = md5(serialize($resource));
            $result = in_array($resourceKey, static::$optimizedResources) || !$this->isResourceKeyInCookie($resourceKey);

            if ($result) {
                $this->addResourceKeyToCookie($resourceKey);
                static::$optimizedResources[] = $resourceKey;

                return file_exists($resource['file']);
            }
        }

        return false;
    }

    /**
     * Add resource key to cookies
     *
     * @param $resourceKey
     */
    protected function addResourceKeyToCookie($resourceKey)
    {
        $request = \XLite\Core\Request::getInstance();

        $viewedResources = $request->viewedResources;

        if (!empty($viewedResources) && !is_array($viewedResources)) {
            $viewedResources = @json_decode($viewedResources);
        }

        if (!is_array($viewedResources)) {
            $viewedResources = [];
        }

        // leave only 50 last resources hashes in cookie
        $viewedResources = array_slice($viewedResources, -50);

        $viewedResources[] = $resourceKey;
        $request->setCookie('viewedResources', json_encode(array_unique($viewedResources)), 3600);
        $request->viewedResources = $viewedResources;
    }

    /**
     * Check if resource key is in cookies
     *
     * @param $resourceKey
     *
     * @return bool
     */
    protected function isResourceKeyInCookie($resourceKey)
    {
        $request = \XLite\Core\Request::getInstance();

        $viewedResources = $request->viewedResources;

        if (!empty($viewedResources) && !is_array($viewedResources)) {
            $viewedResources = @json_decode($viewedResources);
        }

        if (is_array($viewedResources)) {
            return in_array($resourceKey, $viewedResources);
        }

        return false;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}
