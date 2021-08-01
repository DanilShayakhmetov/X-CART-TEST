<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Decorator\Utils\CacheManager;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Less\ImportPathResolver;

/**
 * LESS parser wrapper
 */
class LessParser extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    const LESS_PARENT_CONDITION = '~parent';

    /**
     * Less parser object
     *
     * @var \Less_Parser
     */
    protected $parser;

    /**
     * Http or https
     *
     * @var mixed
     */
    protected $http = null;

    /**
     * admin or default interface.
     * If none is defined then interface will be detected via \XLite::isAdminZone() method
     *
     * @var mixed
     */
    protected $interface = null;

    /**
     * LESS resource hash
     *
     * @var mixed
     */
    protected $LESSResourceHash;

    /**
     * @var ImportPathResolver
     */
    protected $importPathResolver;

    public function __construct()
    {
        $this->importPathResolver = new ImportPathResolver();
    }

    /**
     * Defines the cache dir for the media type
     *
     * @param string  $media    Media type
     * @param boolean $original Get original path OPTIONAL
     *
     * @return string
     */
    protected function getCacheDir($media, $original = false)
    {
        $interface = is_null($this->interface) ? (\XLite::isAdminZone() ? 'admin' : 'default') : $this->interface;
        $http = is_null($this->http) ? (Request::getInstance()->isHTTPS() ? 'https' : 'http') : $this->http;

        return CacheManager::getResourcesDir($original)
            . $interface . LC_DS
            . $http . LC_DS
            . $media . LC_DS;
    }

    /**
     * Interface setter
     *
     * @param string $interface The interface which will be used for less generation. Can be
     *                          'admin', 'default'.
     *
     * @return void
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;
    }

    /**
     * Http or https setter
     *
     * @param string $http Can be 'http' or 'https'
     *
     * @return void
     */
    public function setHttp($http)
    {
        $this->http = $http;
    }

    /**
     * Make a css file compiled from the LESS files collection
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return array
     */
    public function makeCSS($lessFiles)
    {
        $file = $this->makeLESSResourcePath($lessFiles);
        $path = $this->getCSSResource($lessFiles);
        $url = $this->getCSSResourceURL($path);

        $data = [
            'file'  => $path,
            'media' => 'screen', // It is hardcoded right now
            'url'   => $url,
        ];

        if ($this->needToCompileLessResource($lessFiles)) {
            try {
                $originalPath = $this->getCSSResource($lessFiles, true);
                if (
                    $path != $originalPath
                    && $this->getLESSResourceHash($lessFiles, true)
                    && $this->getLESSResourceHash($lessFiles, true) == $this->calcLESSResourceHash($lessFiles)
                    && \Includes\Utils\FileManager::isFileReadable($originalPath)
                ) {
                    $content = \Includes\Utils\FileManager::read($originalPath);

                } else {
                    // Need recreate parser for every parseFile
                    $this->parser = new \Less_Parser($this->getLessParserOptions());
                    $this->parser->parseFile($file, '');
                    $this->parser->ModifyVars($this->getModifiedLESSVars($data));

                    $content = $this->prepareLESSContent($this->parser->getCss(), $path, $data);
                    $this->setLESSResourceHash($lessFiles);
                }

                \Includes\Utils\FileManager::mkdirRecursive(dirname($path));
                \Includes\Utils\FileManager::write($path, $content);

            } catch (\Exception $e) {
                \XLite\Logger::getInstance()->registerException($e);
                $data = null;
            }
        }

        return $data;
    }

    /**
     * Calc LESSResourceHash
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return array
     */
    protected function calcLESSResourceHash($lessFiles)
    {
        $result = [];

        if ($lessFiles && is_array($lessFiles)) {
            foreach ($lessFiles as $v) {
                $result[$this->getShortName($v['file'])] = $this->calcLESSFileHash($v['file']);
            }
        }

        return $result;
    }

    /**
     * Calc LESSResourceHash
     *
     * @param string $lessFile LESS file path
     *
     * @return string
     */
    protected function calcLESSFileHash($lessFile)
    {
        return $this->executeCachedRuntime(function () use ($lessFile) {
            $result = '';

            if ($lessFile && file_exists($lessFile)) {
                $content = file_get_contents($lessFile);
                $offset = 0;

                while (($offset = strpos($content, '@import ', $offset)) !== false) {
                    $import = substr($content, $offset + 8, strpos($content, ';', $offset) - $offset - 8);
                    $import = trim($import, "\n\r ");
                    preg_match("#^(\([^)]+\))?+(.*)$#", $import, $matches);
                    $import = trim($matches[2], "\n\r'\" ;@");

                    if ($import === static::LESS_PARENT_CONDITION) {
                        @list($path, ) = $this->importPathResolver->getParentPathAndUri($lessFile, '');
                        $result .= $this->calcLESSFileHash($path);
                    } else {
                        $result .= $this->calcLESSFileHash(sprintf("%s/%s", dirname($lessFile), $import));
                    }

                    $offset++;
                }

                $result = md5($result . md5_file($lessFile));
            }

            return $result;
        }, $lessFile);
    }

    /**
     * Set LESSResourceHash
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return void
     */
    protected function setLESSResourceHash($lessFiles)
    {
        $this->LESSResourceHash[$this->getCSSResource($lessFiles, false, true)] = $this->calcLESSResourceHash($lessFiles);
        $this->LESSResourceHash[$this->getCSSResource($lessFiles, true, true)] = $this->calcLESSResourceHash($lessFiles);
        \Includes\Utils\FileManager::write(
            static::getHashFilePath(),
            '; <' . '?php /*' . PHP_EOL . serialize($this->LESSResourceHash) . '; */ ?' . '>'
        );
    }

    /**
     * Get LESSResourceHash
     *
     * @param array   $lessFiles LESS files structures array
     * @param boolean $original  Get original path OPTIONAL
     *
     * @return array
     */
    protected function getLESSResourceHash($lessFiles, $original = false)
    {
        if (!isset($this->LESSResourceHash)) {
            $data = \Includes\Utils\FileManager::read(static::getHashFilePath());

            if ($data) {
                $data = substr($data, strlen('; <' . '?php /*' . PHP_EOL), strlen('; */ ?' . '>') * -1);
                $data = unserialize($data);
            }

            $this->LESSResourceHash = $data && is_array($data)
                ? $data
                : [];
        }

        $path = $this->getCSSResource($lessFiles, $original, true);

        return isset($this->LESSResourceHash[$path]) && is_array($this->LESSResourceHash[$path])
            ? $this->LESSResourceHash[$path]
            : [];
    }

    /**
     * Get file path with fixtures paths
     *
     * @return string
     */
    protected static function getHashFilePath()
    {
        return LC_DIR_VAR . '.lessFiles.php';
    }

    /**
     * Create a unique name for the less files collection
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return string
     */
    protected function getUniqueName($lessFiles)
    {
        $list = [];

        foreach ($lessFiles as $id => $lessFile) {
            unset($lessFile['file']);
            $list[$lessFile['original']] = $lessFile;
        }

        ksort($list);

        return hash('md4', serialize($list));
    }

    /**
     * Create a main less file for the provided less files collection
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return string LESS file name
     */
    protected function makeLESSResourcePath($lessFiles)
    {
        $filePath = $this->getCacheDir('screen') . $this->getUniqueName($lessFiles) . '.less';

        if (!is_file($filePath)) {
            $content = '';

            foreach ($lessFiles as $resource) {
                $resourcePath = \Includes\Utils\FileManager::makeRelativePath($this->getCacheDir('screen'), $resource['file']);

                if (isset($resource['reference'])) {
                    $content .= "\r\n" . '@import (reference) "' . str_replace('/', LC_DS, $resourcePath) . '";' . "\r\n";
                } else {
                    $content .= "\r\n" . '@import "' . str_replace('/', LC_DS, $resourcePath) . '";' . "\r\n";
                }
            }

            \Includes\Utils\FileManager::mkdirRecursive(dirname($filePath));
            \Includes\Utils\FileManager::write($filePath, $content);
        }

        return $filePath;
    }

    /**
     * Defines the name for the CSS resource
     * CSS resource is compilation of the provided LESS files
     *
     * @param array   $lessFiles LESS files structures array
     * @param boolean $original  Get original path OPTIONAL
     * @param boolean $shortName Get short name OPTIONAL
     *
     * @return string
     */
    protected function getCSSResource($lessFiles, $original = false, $shortName = false)
    {
        $result = $this->getCacheDir('screen', $original) . $this->getUniqueName($lessFiles) . '.css';

        return $shortName
            ? $this->getShortName($result)
            : $result;
    }

    /**
     * Return short name
     *
     * @param string $path Path
     *
     * @return string
     */
    protected function getShortName($path)
    {
        return str_replace(LC_DIR, '', $path);
    }

    /**
     * Defines the URL for the CSS resource
     *
     * @param string $path File path to the CSS resource
     *
     * @return string
     */
    protected function getCSSResourceURL($path)
    {
        return \XLite::getInstance()->getShopURL(
            str_replace(LC_DS, '/', substr(dirname($path), strlen(LC_DIR_ROOT))) . '/' . basename($path)
        );
    }

    /**
     * Check if the less resource must be compiled
     *
     * @param array $lessFiles LESS files structures array
     *
     * @return boolean
     */
    protected function needToCompileLessResource($lessFiles)
    {
        $file = $this->getCSSResource($lessFiles);
        $hash = $this->getLESSResourceHash($lessFiles);

        return !file_exists($file)
            || empty($hash)
            || $hash != $this->calcLESSResourceHash($lessFiles);
    }

    /**
     * Prepare LESS content
     *
     * @param string $content Content
     * @param string $path    Path
     * @param array  $data    Resource
     *
     * @return string
     */
    protected function prepareLESSContent($content, $path, array $data)
    {
        $file = $data['file'];
        $rootURL = \XLite::getInstance()->getShopURL('');

        $container = $this;

        return preg_replace_callback(
            '/url\(([^)]+)\)/Ss',
            function (array $matches) use ($container, $file, $rootURL) {
                return $container->processCSSURLHandler($matches, $file, $rootURL);
            },
            $content
        );
    }

    /**
     * Process CSS URL callback
     *
     * @param array  $matches Matches
     * @param string $file    File
     *
     * @return string
     */
    public function processCSSURLHandler(array $matches, $file, $rootURL)
    {
        $url = trim($matches[1]);
        $first = substr($url, 0, 1);

        if ('"' == $first || '\'' == $first) {
            $url = stripslashes(substr($url, 1, -1));
        }

        if ($rootURL && strpos($url, $rootURL) === 0) {
            $url = str_replace(LC_DS, '/', \Includes\Utils\FileManager::makeRelativePath($file, LC_DIR_ROOT . substr($url, strlen($rootURL))));
        }

        return 'url("' . $url . '")';
    }

    /**
     * Define the new LESS variables for the specific resource
     *
     * @param array $data Resource data
     *
     * @return array
     */
    protected function getModifiedLESSVars($data)
    {
        $xlite = \XLite::getInstance();
        $layout = Layout::getInstance();

        return [
            // Defines the admin skin path
            'admin-skin'    => '\'' . $xlite->getShopURL(
                    dirname($layout->getResourceWebPath('body.twig', Layout::WEB_PATH_OUTPUT_URL, \XLite::ADMIN_INTERFACE))
                ) . '\'',
            'customer-skin' => '\'' . $xlite->getShopURL(
                    dirname($layout->getResourceWebPath('body.twig', Layout::WEB_PATH_OUTPUT_URL, \XLite::CUSTOMER_INTERFACE))
                ) . '\'',
            'common-skin'   => '\'' . $xlite->getShopURL(
                    $layout->getResourceWebPath('', Layout::WEB_PATH_OUTPUT_URL, \XLite::COMMON_INTERFACE)
                ) . '\'',
        ];
    }

    /**
     * Get Less_Parser options
     *
     * @return array
     */
    protected function getLessParserOptions()
    {
        return [
            'compress'        => true,
            'cache_dir'       => LC_DIR_DATACACHE . '/less',
            'import_callback' => $this->getImportCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function getImportCallback()
    {
        return function (\Less_Tree_Import $import) {
            $filePath = $import->currentFileInfo['filename'] ?? null;
            $entryDir = $import->currentFileInfo['entryPath'] ?? null;

            if (($path = $import->path) instanceof \Less_Tree_Quoted) {
                $path = $path->value;
            }

            return $path === static::LESS_PARENT_CONDITION
                ? $this->importPathResolver->getParentPathAndUri($filePath, $entryDir)
                : $this->importPathResolver->getImportPathAndUri(dirname($filePath) . '/' . $import->getPath(), $entryDir);
        };
    }
}
