<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\Core;

use ReCaptcha\RequestMethod\CurlPost;

/**
 * Class ReCaptcha
 */
class ReCaptcha extends \XLite\Base\Singleton
{
    /**
     * Private (secret) key
     *
     * @var null
     */

    protected $privateKey;

    /**
     * Public (site) key
     *
     * @var null
     */
    protected $publicKey;

    /**
     * recaptcha version
     *
     * @var int
     */
    protected $version;

    /**
     * @var \ReCaptcha\ReCaptcha
     */
    protected $reCaptchaInstance;

    /**
     * Constructor
     */
    protected function __construct()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->ContactUs;

        $this->setPrivateKey($config->recaptcha_private_key);
        $this->setPublicKey($config->recaptcha_public_key);
        $this->setVersion($config->recaptcha_version);

        static::preloadIncludes();
    }

    /**
     * Return ReCaptcha
     *
     * @return \ReCaptcha\ReCaptcha
     */
    protected function getReCaptcha()
    {
        if (null === $this->reCaptchaInstance) {
            $this->reCaptchaInstance = new \ReCaptcha\ReCaptcha($this->getPrivateKey(), new CurlPost());
        }

        return $this->reCaptchaInstance;
    }

    /**
     * Verify response
     *
     * @param $response
     *
     * @return null|\ReCaptcha\Response
     */
    public function verify($response)
    {
        return $this->getReCaptcha() ? $this->getReCaptcha()->verify($response) : null;
    }

    /**
     * Check if this configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return strlen($this->getPrivateKey()) && strlen($this->getPublicKey()) && $this->getVersion();
    }

    /**
     * Return PrivateKey
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return (string)$this->privateKey;
    }

    /**
     * Set PrivateKey
     *
     * @param string $privateKey
     *
     * @return $this
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * Return PublicKey
     *
     * @return string
     */
    public function getPublicKey()
    {
        return (string)$this->publicKey;
    }

    /**
     * Return recaptcha version
     *
     * @return int
     */
    public function getVersion()
    {
        return (int)$this->version;
    }

    /**
     * Set PublicKey
     *
     * @param string $publicKey
     *
     * @return $this
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * Set recaptcha version
     *
     * @param int $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = (int)$version;
        return $this;
    }

    /**
     * Load ReCaptcha autoloader
     *
     * @return void
     */
    protected static function preloadIncludes()
    {
        include_once LC_DIR_MODULES . 'CDev' . LC_DS . 'ContactUs' . LC_DS . 'lib' . LC_DS . 'autoload.php';
    }
}