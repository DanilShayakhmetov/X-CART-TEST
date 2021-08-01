<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Core;

use XLite\Core\Config;

/**
 * Order history main point of execution
 */
class AmazonS3 extends \XLite\Base\Singleton
{
    const DEFAULT_REGION = 'us-east-1';

    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * AWS S3 client
     *
     * @var \S3
     */
    protected static $client;

    /**
     * AWS S3 client config
     *
     * @var \XLite\Core\CommonCell
     */
    protected static $config;

    /**
     * Valid status
     *
     * @var boolean
     */
    protected $valid = false;

    /**
     * URL prefix
     *
     * @var string
     */
    protected static $urlPrefix;

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        $config = static::getConfig();

        if ($config->access_key && $config->secret_key && $config->bucket && function_exists('curl_init')) {

            static::preloadIncludes();

            try {
                $this->valid = $this->checkSettings($config->bucket);

            } catch (\Exception $e) {
                \XLite\Logger::getInstance()->registerException($e);
            }
        }
    }

    /**
     * Check valid status
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Get module options
     *
     * @return \XLite\Core\CommonCell
     */
    protected static function getConfig()
    {
        if (!static::$config) {
            static::$config = new \XLite\Core\CommonCell([
                'storage_type' => Config::getInstance()->CDev->Egoods->storage_type,
                'access_key'   => Config::getInstance()->CDev->Egoods->amazon_access,
                'secret_key'   => Config::getInstance()->CDev->Egoods->amazon_secret,
                'bucket'       => Config::getInstance()->CDev->Egoods->bucket,
                'region'       => Config::getInstance()->CDev->Egoods->bucket_region,
                'link_ttl'     => Config::getInstance()->CDev->Egoods->ttl,
                'do_endpoint'  => Config::getInstance()->CDev->Egoods->do_endpoint
            ]);
        }

        return static::$config;
    }

    /**
     * Set module options
     */
    protected static function setConfig($key, $value)
    {
        if ($key === 'region') {
            static::$config->region = $value;
            static::$client = null;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'CDev\\Egoods',
                    'name'     => 'bucket_region',
                    'value'    => $value,
                )
            );
        }
    }

    /**
     * Read
     *
     * @param string $path Short path
     *
     * @return string
     */
    public function getPresignedUrl($path)
    {
        $result = null;

        try {
            $config = static::getConfig();
            $command = static::getClient()->getCommand(
                'GetObject',
                array(
                    'Bucket' => $config->bucket,
                    'Key'    => $path,
                )
            );

            if (is_int($config->link_ttl) && $config->link_ttl > 0 && $config->link_ttl < 7) {
                $result = $command->createPresignedUrl('+' . $config->link_ttl . ' days');
            } else {
                $result = $command->createPresignedUrl('+7 days');
            }

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Check - file is exists or not
     *
     * @param string $path Short path
     *
     * @return boolean
     */
    public function isExists($path)
    {
        return static::getClient()->doesObjectExist(static::getConfig()->bucket, $path);
    }

    /**
     * Check settings
     *
     * @param string $bucket    S3 bucket
     * @param string $accessKey AWS access key OPTIONAL
     * @param string $secretKey AWS secret key OPTIONAL
     * @param string $endpoint  push endpoint if its DO Cloud
     *
     * @return boolean
     */
    public function checkSettings($bucket, $accessKey = null, $secretKey = null, $endpoint = null)
    {
        $valid = false;

        $client = (!empty($accessKey) || !empty($secretKey))
            ? static::getS3Client($accessKey, $secretKey, null, $endpoint)
            : static::getClient();

        if ($client) {
            $region = $this->detectBucketLocation($client, $bucket);

            if (isset($region)) {
                $valid = true;
                if ($this->getConfig()->region != $region) {
                    $this->setConfig('region', $region);
                }
            }
        }

        return $valid;
    }

    /**
     * Detect and return bucket location (region)
     *
     * @param \Aws\S3\S3Client $client S3 client
     * @param string           $bucket
     *
     * @return string
     */
    protected function detectBucketLocation($client, $bucket)
    {
        $location = null;

        try {

            $command = $client->getCommand(
                'GetBucketLocation',
                array(
                    'Bucket' => $bucket,
                )
            );

            $result = $command->getResult();

            if ($result && isset($result['Location'])) {
                $location = $result['Location'];
            }

        } catch (\Exception $e) {
        }

        return $location;
    }

    // {{{ Service methods

    /**
     * Get client
     *
     * @return \Aws\S3\S3Client
     */
    protected static function getClient()
    {
        if (!static::$client) {

            $config = static::getConfig();

            $region = $config->region ?: null;
            $endpoint = $config->storage_type === 'dos' ? $config->do_endpoint : null;

            static::$client = static::getS3Client($config->access_key, $config->secret_key, $region, $endpoint);
        }

        return static::$client;
    }

    /**
     * Create S3 client object
     *
     * @return \Aws\S3\S3Client
     */
    protected static function getS3Client($key, $secret, $region = null, $endpoint = null)
    {
        static::preloadIncludes();

        if (empty($region)) {
            $region = static::DEFAULT_REGION;
        }

        $params = [
            'key'       => $key,
            'secret'    => $secret,
            'signature' => 'v4',
            'region'    => $endpoint ? explode('.', $endpoint)[0] : $region,
        ];

        if ($endpoint) {
            $params['base_url'] = 'https://' . $endpoint;
        }

        return \Aws\S3\S3Client::factory($params);
    }

    /**
     * Load AWS SDK autoloader
     *
     * @return void
     */
    protected static function preloadIncludes()
    {
        if (!class_exists('Aws\S3\S3Client', false)) {
            include_once LC_DIR_MODULES . 'CDev' . LC_DS . 'Egoods' . LC_DS . 'lib' . LC_DS . 'AWSSDK' . LC_DS . 'aws-autoloader.php';
        }
    }

    // }}}
}