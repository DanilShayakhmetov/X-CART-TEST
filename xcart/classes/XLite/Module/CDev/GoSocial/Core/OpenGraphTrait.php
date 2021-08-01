<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Core;


trait OpenGraphTrait
{
    /**
     * Get Open Graph meta tags
     *
     * @param boolean $preprocessed Preprocessed OPTIONAL
     *
     * @return string
     */
    public function getOpenGraphMetaTags($preprocessed = true)
    {
        $tags = $this->getOgMeta() ?: $this->generateOpenGraphMetaTags();

        return $preprocessed ? $this->preprocessOpenGraphMetaTags($tags) : $tags;
    }

    /**
     * Preprocess Open Graph meta tags
     *
     * @param string $tags Tags content
     *
     * @return string
     */
    abstract protected function preprocessOpenGraphMetaTags($tags);

    /**
     * @return bool
     */
    protected function isUseFacebookOG()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id
               || \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_admins;
    }

    /**
     * Returns open graph app id
     *
     * @return string
     */
    protected function getOpenGraphFacebookAppId()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id;
    }

    /**
     * Returns open graph admins
     *
     * @return string
     */
    protected function getOpenGraphFacebookAdmins()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_admins;
    }

    /**
     * @return bool
     */
    protected function isUseTwitterOG()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_use
               && \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_via;
    }

    /**
     * Define Open Graph meta tags
     *
     * @return array
     */
    protected function defineOpenGraphMetaTags()
    {
        $list = [
            'og:title'       => $this->getOpenGraphTitle(),
            'og:type'        => $this->getOpenGraphType(),
            'og:url'         => $this->getOpenGraphURL(),
            'og:site_name'   => $this->getOpenGraphSiteName(),
            'og:description' => $this->getOpenGraphDescription(),
            'og:locale'      => $this->getOpenGraphLocale(),
        ];

        if ($this->isUseOpenGraphImage()) {
            $list['og:image'] = $this->getOpenGraphImage();

            if ($this->getOpenGraphImageWidth()) {
                $list['og:image:width'] = $this->getOpenGraphImageWidth();
            }

            if ($this->getOpenGraphImageHeight()) {
                $list['og:image:height'] = $this->getOpenGraphImageHeight();
            }
        }

        if ($this->isUseFacebookOG()) {
            $appId = $this->getOpenGraphFacebookAppId();
            $admins = $this->getOpenGraphFacebookAdmins();
            if ($appId) {
                $list['fb:app_id'] = $appId;

            } elseif ($admins) {
                $list['fb:admins'] = $admins;
            }
        }

        if ($this->isUseTwitterOG()) {
            $list = array_merge($list, $this->defineTwitterOpenGraphMetaTags());
        }

        return $list + $this->defineAdditionalMetaTags();
    }

    /**
     * @return array
     */
    protected function defineAdditionalMetaTags()
    {
        return [];
    }

    /**
     * Get generated Open Graph meta tags
     *
     * @return string
     */
    protected function generateOpenGraphMetaTags()
    {
        $list = $this->defineOpenGraphMetaTags();

        $html = [];
        foreach ($list as $k => $v) {
            $html[] = '<meta property="' . $k . '" content="' . htmlentities($v, ENT_COMPAT, 'UTF-8') . '" />';
        }

        return implode("\n", $html);
    }

    /**
     * Define Open Graph meta tags for twitter
     *
     * @return array
     */
    protected function defineTwitterOpenGraphMetaTags()
    {
        $list = [
            'twitter:card'              => 'summary',
            'twitter:title'             => $this->getOpenGraphTitle(),
            'twitter:site'              => \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_via,
            'twitter:description'       => $this->getOpenGraphDescription() ?: $this->getOpenGraphTitle(),
        ];

        if ($this->isUseOpenGraphImage()) {
            $list['twitter:image'] = $this->getOpenGraphImage();
        }

        return $list;
    }

    /**
     * Returns open graph title
     *
     * @return string
     */
    abstract protected function getOpenGraphTitle();

    /**
     * Returns open graph type
     *
     * @return string
     */
    abstract protected function getOpenGraphType();

    /**
     * Returns open graph url
     *
     * @return string
     */
    protected function getOpenGraphURL()
    {
        return '[PAGE_URL]';
    }

    /**
     * Returns open graph site name
     *
     * @return string
     */
    protected function getOpenGraphSiteName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Returns open graph description
     *
     * @return string
     */
    abstract protected function getOpenGraphDescription();

    /**
     * Returns open graph locale
     *
     * @return string
     */
    protected function getOpenGraphLocale()
    {
        return \XLite\Core\Session::getInstance()->getLocale();
    }

    /**
     * Is use OG image
     *
     * @return mixed
     */
    abstract protected function isUseOpenGraphImage();

    /**
     * Returns open graph image
     *
     * @return string
     */
    protected function getOpenGraphImage()
    {
        return '[IMAGE_URL]';
    }

    /**
     * Returns open graph image width
     *
     * @return string
     */
    protected function getOpenGraphImageWidth()
    {
        return 0;
    }

    /**
     * Returns open graph image height
     *
     * @return string
     */
    protected function getOpenGraphImageHeight()
    {
        return 0;
    }
}