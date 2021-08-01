<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Auth;

use Exception;
use Firebase\JWT\JWT;
use InvalidArgumentException;
use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class TokenService
{
    public const TOKEN_READ_ONLY = 'read_only_access';

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $legacyPrivateKey;

    /**
     * @param Application $app
     *
     * @return static
     * @throws InvalidArgumentException
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(Application $app)
    {
        $legacyPrivateKey = $app['config']['jwt_secret_key_legacy'] ?? '';

        return new self($app['config']['jwt_secret_key'], $legacyPrivateKey);
    }

    /**
     * @param string $privateKey
     * @param string $legacyPrivateKey
     *
     * @throws InvalidArgumentException
     */
    public function __construct($privateKey, $legacyPrivateKey = '')
    {
        $this->privateKey = $privateKey;
        $this->legacyPrivateKey = $legacyPrivateKey;
    }

    /**
     * @param array|null $additionalData
     *
     * @return string
     */
    public function generateToken(?array $additionalData = null): string
    {
        $token = [
            'name' => uniqid('bus', true),
        ];

        if ($additionalData !== null) {
            $token = array_merge(
                $token,
                $additionalData
            );
        }

        return JWT::encode($token, $this->privateKey);
    }

    /**
     * @param string $token
     *
     * @return array|null
     */
    public function decodeToken($token): ?array
    {
        try {
            $result = (array) JWT::decode($token, $this->privateKey, ['HS256']);

        } catch (Exception $e) {
            $result = null;
        }

        if (!$result && $this->legacyPrivateKey) {
            try {
                $result = (array) JWT::decode($token, $this->legacyPrivateKey, ['HS256']);

            } catch (Exception $e) {
                $result = null;
            }
        }

        return $result;
    }

    /**
     * @param $tokenData
     *
     * @return bool
     */
    public function isReadOnlyToken($tokenData): bool
    {
        return !empty($tokenData[self::TOKEN_READ_ONLY]);
    }
}
