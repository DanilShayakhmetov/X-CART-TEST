<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Clean URLs Router
 * TODO: Refactor Controllers, CleanURL repo, etc. move routing logic to router
 */
class Router extends \XLite\Base\Singleton
{
    /**
     * @var array
     */
    protected $activeLanguagesCodes = null;


    /**
     * @var bool
     */
    protected $isUseLangUrlsTmp = true;

    /**
     * Temporarily disable language urls
     */
    public function disableLanguageUrlsTmp()
    {
        $this->isUseLangUrlsTmp = false;
    }

    /**
     * Release disabling flag
     */
    public function releaseLanguageUrlsTmp()
    {
        $this->isUseLangUrlsTmp = true;
    }

    /**
     * Process \XLite\Core\Request data
     */
    public function processCleanUrls()
    {
        $request = $this->getRequest();

        if (LC_USE_CLEAN_URLS) {
            //if new .htaccess else old
            if (empty($request->rest) && empty($request->last) && empty($request->ext) && !empty($request->url)) {
                $this->processCleanUrlLanguage();

                // Remove unnecessary running script name
                $request->url = str_replace(\XLite::getInstance()->getScript(), '', $request->url);

                preg_match(
                    '#^((([./\w-]+)/)?([.\w-]+?)/)?([.\w-]+?)(/?)(\.([\w-]+))?$#ui',
                    $request->url,
                    $matches
                );

                $_GET['rest'] = isset($matches[3]) ? $matches[3] : null;
                $_GET['last'] = isset($matches[4]) ? $matches[4] : null;
                $_GET['url'] = isset($matches[5]) ? $matches[5] : null;
                $_GET['ext'] = isset($matches[7]) ? $matches[7] : null;
                \XLite\Core\Request::getInstance()->mapRequest();
            } else {
                $this->processCleanUrlLanguage();
            }
        }
    }

    /**
     * Process \XLite\Core\Request, detect and set language
     */
    public function processCleanUrlLanguage()
    {
        if ($this->isUseLanguageUrls()) {
            $request = $this->getRequest();

            //if new .htaccess else old
            if (empty($request->rest) && empty($request->last) && empty($request->ext) && !empty($request->url)) {
                if (preg_match('#^([a-z]{2})(/|$)#i', $request->url, $matches) && in_array($matches[1], $this->getActiveLanguagesCodes())) {
                    $request->setLanguageCode($matches[1]);
                    $request->url = substr($request->url, 3);
                }
            } else {
                if (preg_match('#^([a-z]{2})(/|$)#i', $request->rest, $matches) && in_array($matches[1], $this->getActiveLanguagesCodes())) {
                    $request->setLanguageCode($matches[1]);
                    $request->last = substr($request->last, 3);
                } elseif (preg_match('#^([a-z]{2})(/|$)#i', $request->last, $matches) && in_array($matches[1], $this->getActiveLanguagesCodes())) {
                    $request->setLanguageCode($matches[1]);
                    $request->last = substr($request->last, 3);
                }
            }
        }
    }

    /**
     * Is use language urls
     *
     * @return bool
     */
    public function isUseLanguageUrls()
    {
        return $this->isUseLangUrlsTmp
               && \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'use_language_url']) == 'Y';
    }

    /**
     * Return array of codes for currently active languages
     *
     * @return array
     */
    public function getActiveLanguagesCodes()
    {
        if (null === $this->activeLanguagesCodes) {
            $result = [];

            foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
                $result[] = $language->getCode();
            }

            $this->activeLanguagesCodes = $result;
        }

        return $this->activeLanguagesCodes;
    }

    /**
     * Return request object
     *
     * @return \XLite\Core\Request
     */
    public function getRequest()
    {
        return \XLite\Core\Request::getInstance();
    }
}
