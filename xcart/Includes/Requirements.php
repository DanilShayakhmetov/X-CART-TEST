<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

/**
 * Class Requirements
 *
 * @package Includes
 */
final class Requirements
{
    const LEVEL_OPTIONAL = 1; // 0b0001;
    const LEVEL_MINOR    = 2; // 0b0010;
    const LEVEL_MAJOR    = 4; // 0b0100;
    const LEVEL_CRITICAL = 8; // 0b1000;

    const STATE_UNCHECKED = 0; // 0b0000;
    const STATE_CHECKED   = 1; // 0b0001;
    const STATE_SUCCESS   = 3; // 0b0011;
    const STATE_FAILURE   = 5; // 0b0101;
    const STATE_SKIPPED   = 13; // 0b1101;

    const PERMISSION_DIR  = '777';
    const PERMISSION_FILE = '666';

    /** @var array */
    private $environment = [];

    /** @var array */
    private $requirements = [];

    /** @var boolean */
    private $checked = false;

    /** @var array */
    private $phpInfo;

    /** @var array */
    private $config;

    /** @var \PDO */
    private $pdo;

    /**
     * Environment params:
     *
     * rootPath => LC_DIR_ROOT (install_script)
     *
     * configPath => LC_DIR_CONFIG (config_file)
     * configFileName => LC_CONFIG_FILE (config_file)
     * defaultConfigFileName => LC_DEFAULT_CONFIG_FILE (config_file)
     *
     * minPhpVersion => LC_PHP_VERSION_MIN (php_version)
     * maxPhpVersion => LC_PHP_VERSION_MAX (php_version)
     * forbiddenPhpVersions => $lcSettings['forbidden_php_versions'] (php_version)
     *
     * requiredFunctions => ? (php_disabled_functions)
     *
     * memoryLimitMin => LC_PHP_MEMORY_LIMIT_MIN (php_memory_limit)
     *
     * filePermissionsPaths => $lcSettings['mustBeWritable'] (file_permissions)
     *
     * minMysqlVersion => LC_MYSQL_VERSION_MIN
     * minMariadbVersion => LC_MARIADB_VERSION_MIN
     *
     * databaseDetails
     *
     * @param array $environment
     */
    public function __construct(array $environment = [])
    {
        $rootPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $default = [
            'rootPath' => $rootPath,

            'configFileName'        => 'config.php',
            'defaultConfigFileName' => 'config.default.php',

            'minPhpVersion'        => '7.2.9',
            'maxPhpVersion'        => '',
            'forbiddenPhpVersions' => [],

            'requiredFunctions' => self::getRequiredFunctions(),

            'memoryLimitMin' => PHP_INT_SIZE === 8 ? '256M' : '128M',

            'filePermissionsPaths' => self::getFilePermissionsPaths(),

            'minMysqlVersion' => '5.7.7',
            'minMariadbVersion' => '10.2.4',
            'databaseDetails' => isset($config['database_details']) ? $config['database_details'] : [],
        ];

        $this->environment = array_merge($default, $environment);

        if (!isset($environment['databaseDetails'])) {
            $config = $this->getConfig();

            $this->environment['databaseDetails'] = isset($config['database_details'])
                ? $config['database_details']
                : [];
        }

        $this->requirements = $this->defineRequirements();
    }

    /**
     * @param array $exclude
     *
     * @return array
     */
    public function getResult(array $exclude = [])
    {
        $result = [];

        if (!$this->checked) {
            $this->check($exclude);
        }

        foreach ($this->requirements as $name => $requirement) {
            if (in_array($name, $exclude, true)) {
                continue;
            }
            $result[$name] = array_intersect_key(
                $requirement,
                array_flip(['title', 'state', 'level', 'description', 'data'])
            );
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getUnchecked()
    {
        $result = [];

        $defaults = [
            'state'       => self::STATE_UNCHECKED,
            'data'        => []
        ];

        foreach ($this->requirements as $name => $requirement) {
            $result[$name] = array_intersect_key(
                array_merge($defaults, $requirement),
                array_flip(['title', 'state', 'level', 'description', 'data'])
            );
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function getSingleResult($name)
    {
        $result = [];

        $this->checkRequirement($name);

        $requirement = isset($this->requirements[$name])
            ? $this->requirements[$name]
            : null;

        if ($requirement) {
            $result = array_intersect_key(
                $requirement,
                array_flip(['title', 'state', 'level', 'description', 'data'])
            );
        }


        return $result;
    }

    public function clear()
    {
        foreach ($this->requirements as $name => $requirement) {
            $this->mergeRequirement($name, [
                'state' => self::STATE_UNCHECKED,
                'data'  => [],
            ]);
        }

        $this->checked = false;
    }

    /**
     * @param integer $level
     *
     * @return boolean
     */
    public function hasError($level = self::LEVEL_CRITICAL)
    {
        foreach ($this->getResult() as $name => $result) {
            if (($level === null || $result['level'] & $level) && $result['state'] & self::STATE_FAILURE) {
                return true;
            }
        }

        return false;
    }

    // {{{ Define requirements

    /**
     * @return array
     */
    private function defineRequirements()
    {
        return [
            // todo: remove, unused
            'install_script'           => [
                'title'       => 'Installation script',
                'state'       => self::STATE_UNCHECKED,
                'level'       => self::LEVEL_OPTIONAL,
                'environment' => [
                    'rootPath', // LC_DIR_ROOT
                ],
                'checker'     => $this->getInstallScriptChecker(),
            ],
            'config_file'              => [
                'title'       => 'Config file',
                'state'       => self::STATE_UNCHECKED,
                'level'       => self::LEVEL_CRITICAL,
                'environment' => [
                    'rootPath', // LC_DIR_ROOT
                    'configFileName', // LC_CONFIG_FILE
                    'defaultConfigFileName', // LC_DEFAULT_CONFIG_FILE
                ],
                'checker'     => $this->getConfigFileChecker(),
                'fixer'       => $this->getConfigFileFixer(),
            ],
            'php_version'              => [
                'title'       => 'PHP version',
                'state'       => self::STATE_UNCHECKED,
                'level'       => self::LEVEL_CRITICAL,
                'environment' => [
                    'minPhpVersion', // LC_PHP_VERSION_MIN
                    'maxPhpVersion', // LC_PHP_VERSION_MAX
                    'forbiddenPhpVersions', // $lcSettings['forbidden_php_versions']
                ],
                'checker'     => $this->getPhpVersionChecker(),
            ],
            'php_disabled_functions'   => [
                'title'       => 'Disabled functions',
                'state'       => self::STATE_UNCHECKED,
                'level'       => self::LEVEL_OPTIONAL,
                'environment' => [
                    'requiredFunctions', //
                ],
                'checker'     => $this->getPhpDisabledFunctionsChecker(),
            ],
            'php_memory_limit'         => [
                'title'       => 'Memory limit',
                'state'       => self::STATE_UNCHECKED,
                'level'       => self::LEVEL_CRITICAL,
                'environment' => [
                    'memoryLimitMin', // LC_PHP_MEMORY_LIMIT_MIN
                ],
                'checker'     => $this->getPhpMemoryLimitChecker(),
            ],
            'php_file_uploads'         => [
                'title'   => 'File uploads',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_OPTIONAL,
                'checker' => $this->getPhpFileUploadsChecker(),
            ],
            'php_pdo_mysql'            => [
                'title'   => 'PDO extension',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_CRITICAL,
                'checker' => $this->getPhpPdoMysqlChecker(),
            ],
            'php_upload_max_file_size' => [
                'title'   => 'Upload file size limit',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_OPTIONAL,
                'checker' => $this->getPhpUploadMaxFileSizeChecker(),
            ],
            'file_permissions'         => [
                'title'       => 'File permissions',
                'state'       => self::STATE_UNCHECKED,
                'level'       => self::LEVEL_CRITICAL,
                'environment' => [
                    'rootPath', // LC_DIR_ROOT
                    'filePermissionsPaths', // $lcSettings['mustBeWritable']
                ],
                'checker'     => $this->getFilePermissionsChecker(),
            ],
            'mysql_version'            => [
                'title'       => 'MySQL version',
                'state'       => self::STATE_UNCHECKED,
                'level'       => self::LEVEL_CRITICAL,
                'dependency'  => ['php_pdo_mysql'],
                'environment' => [
                    'minMysqlVersion', // LC_MYSQL_VERSION_MIN
                    'minMariadbVersion', // LC_MARIADB_VERSION_MIN
                    'databaseDetails',
                ],
                'checker'     => $this->getMysqlVersionChecker(),
            ],
            'php_gdlib'                => [
                'title'   => 'GDlib extension',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_OPTIONAL,
                'checker' => $this->getPhpGDLibChecker(),
            ],
            'php_phar'                 => [
                'title'   => 'Phar extension',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_OPTIONAL,
                'checker' => $this->getPhpPharChecker(),
            ],
            'https_bouncer'            => [
                'title'   => 'HTTPS bouncers',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_OPTIONAL,
                'checker' => $this->getHttpsBouncerChecker(),
            ],
            'xml_support'              => [
                'title'   => 'XML extensions support',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_OPTIONAL,
                'checker' => $this->getXmlSupportChecker(),
            ],
            'doc_blocks_support'       => [
                'title'   => 'DocBlocks support',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_CRITICAL,
                'checker' => $this->getDocBlocksSupportChecker(),
            ],
            'frame_options'       => [
                'title'   => 'X-Frame-Options',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_CRITICAL,
                'checker' => $this->getFrameOptionsChecker(),
            ],
            'loopback_request'       => [
                'title'   => 'Loopback request',
                'state'   => self::STATE_UNCHECKED,
                'level'   => self::LEVEL_OPTIONAL,
                'checker' => $this->getLoopbackRequestChecker(),
            ],
        ];
    }

    /**
     * @return \Closure
     */
    private function getInstallScriptChecker()
    {
        /** @var string $rootPath */
        $rootPath = $this->getEnvironment('rootPath');

        return function () use ($rootPath) {
            return [
                !@file_exists($rootPath . 'install.php'),
                'error_message_1', // X-Cart installation script is not found. Restore it and try again
                [],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getConfigFileChecker()
    {
        /** @var string $configPath */
        $configPath = $this->getEnvironment('rootPath') . 'etc' . DIRECTORY_SEPARATOR;
        /** @var string $configFileName */
        $configFileName = $this->getEnvironment('configFileName');
        /** @var string $defaultConfigFileName */
        $defaultConfigFileName = $this->getEnvironment('defaultConfigFileName');

        return function () use ($configPath, $configFileName, $defaultConfigFileName) {
            return [
                @file_exists($configPath . $configFileName),
                '',
                [
                    'configPath'            => $configPath,
                    'configFileName'        => $configFileName,
                    'defaultConfigFileName' => $defaultConfigFileName,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getConfigFileFixer()
    {
        /** @var string $configPath */
        $configPath = $this->getEnvironment('rootPath') . 'etc' . DIRECTORY_SEPARATOR;
        /** @var string $configFileName */
        $configFileName = $this->getEnvironment('configFileName');
        /** @var string $defaultConfigFileName */
        $defaultConfigFileName = $this->getEnvironment('defaultConfigFileName');

        return function () use ($configPath, $configFileName, $defaultConfigFileName) {
            return [
                @copy($configPath . $defaultConfigFileName, $configPath . $configFileName),
                'error_message_1', // Config file does not exist and cannot be copied from the default config file. It is required for the installation.<br /><br />Please, follow these steps: <br /><br />1. Go to directory :configPath<br />2. Copy <i>:defaultConfigFileName</i> to <i>:configFileName</i><br />3. Set writeable permissions on <i>:configFileName</i><br /><br />Then try again.
                [
                    'configPath'            => $configPath,
                    'configFileName'        => $configFileName,
                    'defaultConfigFileName' => $defaultConfigFileName,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getPhpVersionChecker()
    {
        /** @var string $minPhpVersion */
        $minPhpVersion = $this->getEnvironment('minPhpVersion');
        /** @var string $maxPhpVersion */
        $maxPhpVersion = $this->getEnvironment('maxPhpVersion');
        /** @var array $forbiddenPhpVersions */
        $forbiddenPhpVersions = $this->getEnvironment('forbiddenPhpVersions');

        return function () use ($minPhpVersion, $maxPhpVersion, $forbiddenPhpVersions) {
            $version = phpversion();

            if (version_compare($version, $minPhpVersion) < 0) {
                return [
                    false,
                    'error_message_1', // PHP Version must be at least :minPhpVersion
                    [
                        'version'       => $version,
                        'minPhpVersion' => $minPhpVersion,
                    ],
                ];
            }

            if ($maxPhpVersion && version_compare($version, $maxPhpVersion) > 0) {
                return [
                    false,
                    'error_message_2', // PHP Version must be not greater than :maxPhpVersion
                    [
                        'version'       => $version,
                        'maxPhpVersion' => $maxPhpVersion,
                    ],
                ];
            }

            if ($forbiddenPhpVersions && is_array($forbiddenPhpVersions)) {
                foreach ($forbiddenPhpVersions as $forbiddenPhpVersion) {
                    if (version_compare($version, $forbiddenPhpVersion['min']) >= 0
                        && version_compare($version, $forbiddenPhpVersion['max']) <= 0
                    ) {
                        return [
                            false,
                            'error_message_3', // Unsupported PHP version detected
                            [
                                'version'                => $version,
                                'forbiddenPhpVersionMin' => $forbiddenPhpVersion['min'],
                                'forbiddenPhpVersionMax' => $forbiddenPhpVersion['max'],
                            ],
                        ];
                    }
                }
            }

            return true;
        };
    }

    /**
     * @return \Closure
     */
    private function getPhpDisabledFunctionsChecker()
    {
        /** @var array $requiredFunctions */
        $requiredFunctions = $this->getEnvironment('requiredFunctions');

        return function () use ($requiredFunctions) {
            $result = ['exists' => [], 'missed' => []];
            foreach ($requiredFunctions as $function) {
                $result[function_exists($function) ? 'exists' : 'missed'][] = $function;
            }

            if (count($result['missed'])) {
                $result['missedFunctions'] = substr(implode(', ', $result['missed']), 0, 45) . '...';
            }

            return [
                !(bool) count($result['missed']),
                'error_message_1', // There are disabled functions (:missedFunctions) that may be used by software in some cases and should be enabled.
                $result,
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getPhpMemoryLimitChecker()
    {
        /** @var string $memoryLimitMin */
        $memoryLimitMin = $this->getEnvironment('memoryLimitMin');

        return function () use ($memoryLimitMin) {
            if ($this->isMemoryLimited()) {
                $memoryLimit = @ini_get('memory_limit');

                return [
                    $this->convertMemoryLimitToInteger($memoryLimitMin) <= $this->convertMemoryLimitToInteger($memoryLimit),
                    'error_message_1', // PHP memory_limit option must be at least :memoryLimitMin
                    [
                        'memoryLimit'    => $memoryLimit,
                        'memoryLimitMin' => $memoryLimitMin,
                    ],
                ];
            } else {
                return [
                    true,
                    'error_message_1', // PHP memory_limit option must be at least :memoryLimitMin
                    [
                        'memoryLimit'    => 'Unlimited',
                        'memoryLimitMin' => $memoryLimitMin,
                    ],
                ];
            }
        };
    }

    /**
     * @return boolean
     */
    private function isMemoryLimited()
    {
        $info = $this->getPhpInfo();

        $unlimited = ($info['no_mem_limit']
                && $info['commands_exists']
                && !function_exists('memory_get_usage')
                // && version_compare(phpversion(), '4.3.2') >= 0
                && @ini_get('memory_limit') === '')
            || @ini_get('memory_limit') === '-1';

        return !$unlimited;
    }

    /**
     * @param string $value
     *
     * @return integer
     */
    private function convertMemoryLimitToInteger($value)
    {
        $last = strtolower(substr($value, -1));
        $value = (integer)$value;

        switch ($last) {
            case 'k':
                $value *= 1024;
                break;

            case 'm':
                $value *= 1024 * 1024;
                break;

            case 'g':
                $value *= 1024 * 1024 * 1024;
        }

        return (int) $value;
    }

    /**
     * @return \Closure
     */
    private function getPhpFileUploadsChecker()
    {
        return function () {
            $value = @ini_get('file_uploads');

            return [
                !in_array(strtolower($value), ['off', '0', '', false], true),
                'error_message_1', // PHP file_uploads option must be set to On
                [
                    'file_uploads' => $value,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getPhpPdoMysqlChecker()
    {
        return function () {
            return [
                class_exists('PDO') && defined('PDO::MYSQL_ATTR_LOCAL_INFILE'),
                'error_message_1', // PDO extension with MySQL support must be installed.
                [],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getPhpUploadMaxFileSizeChecker()
    {
        return function () {
            $value = @ini_get('upload_max_filesize');

            return [
                @ini_get('upload_max_filesize'),
                'error_message_1', // PHP option upload_max_filesize must contain a value. It is currently empty.
                [
                    'upload_max_filesize' => $value,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getFilePermissionsChecker()
    {
        /** @var string $rootPath */
        $rootPath = $this->getEnvironment('rootPath');
        /** @var array $filePermissionsPaths */
        $filePermissionsPaths = $this->getEnvironment('filePermissionsPaths');

        return function () use ($rootPath, $filePermissionsPaths) {
            $result = [];
            foreach ($filePermissionsPaths as $path) {
                $result[] = $this->checkPermissions($rootPath . $path);
            }

            $result = call_user_func_array('array_merge', $result);

            if ($result) {
                $perms = [];
                foreach ($result as $file => $perm) {
                    if (LC_OS_IS_WIN) {
                        $perms[] = $file;

                    } else {
                        if (is_dir($file)) {
                            $perms[] = 'chmod 0' . self::PERMISSION_DIR . ' ' . $file;
                            $perms[] = 'find ' . $file . ' -type d -exec chmod 0' . self::PERMISSION_DIR . ' {} \\;';
                            $perms[] = 'find ' . $file . ' -type f -exec chmod 0' . self::PERMISSION_FILE . ' {} \\;';

                        } else {
                            $perms[] = 'chmod 0' . $perm . ' ' . $file;
                        }
                    }
                    if (count($perms) > 25) {
                        break;
                    }
                }

                return [
                    false,
                    LC_OS_IS_WIN
                        ? 'error_message_1' // Not enough permissions to run the process. Please make sure the following files are writable:<br /><i class="copy2clipboard fa fa-clipboard"></i><div class="copy2clipboard-alert alert-success" style="display: none;"></div><div class="permissions-list">:pathsList</div>
                        : 'error_message_2', // Not enough permissions to run the process. Make sure the following permissions are set (UNIX-like systems only):<br /><i class="copy2clipboard fa fa-clipboard"></i><div class="copy2clipboard-alert alert-success" style="display: none;"></div><div class="permissions-list">:pathsList</div>Such permissions are required for a seamless automated installation or upgrade of X-Cart on your server. They do not take into account the specific configuration of your server or any security requirements. Once the process is completed, make sure you change the permissions to a more restrictive setting. <a target="_blank" href="http://kb.x-cart.com/en/setting_up_x-cart_5_environment/secure_configuration.html#why-x-cart-asks-for-666777-permissions">Read more</a>
                    [
                        'paths'     => $result,
                        'pathsList' => implode('<br />' . PHP_EOL, $perms),
                    ],
                ];
            }

            return true;
        };
    }

    /**
     * @param string $path
     *
     * @return array
     */
    private function checkPermissions($path)
    {
        if (strpos(basename($path), '.') !== 0) {
            if (!is_writable($path)) {
                return [$path => is_dir($path) ? self::PERMISSION_DIR : self::PERMISSION_FILE];
            }

            if (!LC_OS_IS_WIN && is_dir($path) && !is_executable($path)) {
                return [$path => self::PERMISSION_DIR];
            }
        }

        $result = [];
        if (is_dir($path) && $handle = @opendir($path)) {
            while (($file = readdir($handle)) !== false) {
                // Skip '.', '..', '.htaccess' and other files those names starts from '.'
                if (strpos($file, '.') === 0) {
                    continue;
                }

                $fileRealPath = $path . DIRECTORY_SEPARATOR . $file;

                if (!is_writable($fileRealPath)) {
                    $result[$fileRealPath] = is_dir($fileRealPath) ? self::PERMISSION_DIR : self::PERMISSION_FILE;

                } elseif (is_dir($fileRealPath)) {
                    $result = array_merge($result, $this->checkPermissions($fileRealPath));
                }

            }

            closedir($handle);
        }

        return $result;
    }

    /**
     * @return \Closure
     */
    private function getMysqlVersionChecker()
    {
        /** @var string $minMysqlVersion */
        $minMysqlVersion = $this->getEnvironment('minMysqlVersion');

        return function () use ($minMysqlVersion) {
            $pdo = $this->getPDO();

            $result  = true;
            $innodb  = false;
            $version = 'unknown';
            $error   = '';
            $rdbms = 'MySQL';

            if ($pdo) {
                try {
                    $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);

                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }

                if ($version) {
                    if ($this->isMariaDB($version)) {
                        if (preg_match('/1\d\.\d+\.\d+/', $version, $matches)) {
                            $version = $matches[0];
                        }
                        $minMysqlVersion = $this->getEnvironment('minMariadbVersion');
                        $rdbms = 'MariaDB';
                    }
                    if (version_compare($version, $minMysqlVersion) < 0) {
                        return [
                            false,
                            'error_message_1', // MySQL version must be at least :minMysqlVersion.
                            [
                                'version'         => $version,
                                'minMysqlVersion' => $minMysqlVersion,
                                'rdbms'           => $rdbms
                            ],
                        ];

                    } else {
                        foreach ($pdo->query('SHOW ENGINES') as $row) {
                            if (0 === strcasecmp('InnoDB', $row['Engine'])) {
                                $innodb = true;
                                break;
                            }
                        }

                        return [
                            $innodb,
                            'error_message_2', // MySQL server doesn't support InnoDB engine. It is required for X-Cart operation
                            [
                                'version' => $version,
                                'innodb'  => $innodb,
                            ],
                        ];
                    }
                }
            }

            return [
                $result,
                'error_message_3', // Cannot get MySQL server version
                [
                    'version' => $version,
                    'error'   => $error,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getPhpGDLibChecker()
    {
        return function () {
            $result  = false;
            $version = '';

            if (extension_loaded('gd') && function_exists('gd_info')) {
                $gdInfo  = gd_info();
                $version = $gdInfo['GD Version'];
                $result  = preg_match('/\D*2\./', $gdInfo['GD Version']);
            }

            $imageCreateFromJPEG = function_exists('imagecreatefromjpeg');

            return [
                $result && $imageCreateFromJPEG,
                $imageCreateFromJPEG
                    ? 'error_message_1' // GDlib extension v.2.0 or later is required for some modules.
                    : 'error_message_2', // GDlib extension has not JPEG plugin.
                [
                    'version'             => $version,
                    'imagecreatefromjpeg' => $imageCreateFromJPEG,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getPhpPharChecker()
    {
        return function () {
            return [
                extension_loaded('Phar'),
                'error_message_1',
                []
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getHttpsBouncerChecker()
    {
        return function () {
            $result = false;
            $version = '';

            if (function_exists('curl_init') && function_exists('curl_version')) {
                $curlVersion = curl_version();

                if (is_array($curlVersion)) {
                    $version = 'libcurl ' . $curlVersion['version'];
                    if (!empty($curlVersion['ssl_version'])) {
                        $version .= ', ' . $curlVersion['ssl_version'];
                    }
                } else {
                    $version = $curlVersion;
                }

                $result = (!is_array($curlVersion) || in_array('https', $curlVersion['protocols'], true))
                    && (is_array($curlVersion) || preg_match('/ssl|tls/Si', $curlVersion));
            }

            return [
                $result,
                $version
                    ? 'error_message_1' // libcurl extension is found but does not support secure protocols
                    : 'error_message_2', // libcurl extension not found
                [
                    'version' => $version,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getXmlSupportChecker()
    {
        return function () {
            $extensions = [];

            if (function_exists('xml_parse')) {
                $extensions[] = 'XML Parser';
            }

            if (function_exists('dom_import_simplexml')) {
                $extensions[] = 'DOM/XML';
            }

            return [
                count($extensions),
                'error_message_1', // XML/Expat and DOM extensions are required for some modules.
                [
                    'extensions' => $extensions,
                ],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getDocBlocksSupportChecker()
    {
        $rc       = new \ReflectionClass('Includes\Requirements');
        $docBlock = $rc->getDocComment();

        return function () use ($docBlock) {
            $eAccelerator = extension_loaded('eAccelerator');

            return [
                !empty($docBlock) && preg_match('/@package/', $docBlock),
                $eAccelerator
                    ? 'error_message_1' // The DocBlock feature is not supported by your PHP. This feature is required for X-Cart operation.
                    : 'error_message_2', // The DocBlock feature is not supported by your PHP. This feature is required for X-Cart operation. The cause of DocBlock feature being blocked may be the eAccelerator extension. Disable this extension and try again.
                ['eAccelerator' => $eAccelerator],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getFrameOptionsChecker()
    {
        return function () {
            $result = true;

            if (
                function_exists('curl_init')
                && function_exists('curl_version')
                && isset($_SERVER['HTTP_REFERER'])
            ) {
                $host = $_SERVER['HTTP_REFERER'];
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL            => $host,
                    CURLOPT_HEADER         => 1,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/999.99 (KHTML, like Gecko) Chrome/99.0.9999.99 Safari/999.99',
                ]);

                if (
                    ($authUser = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_user']))
                    && ($authPass = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_pass']))
                ) {
                    curl_setopt($curl, CURLOPT_USERPWD, $authUser . ':' . $authPass);
                }

                $resp = curl_exec($curl);

                if (preg_match('/X-Frame-Options:\s.*?(.*?)$/im', $resp, $matches)) {
                    $result = strtoupper(trim($matches[1])) !== 'DENY';
                }

                curl_close($curl);
            }

            return [
                $result,
                'error_message_1', // Your server X-Frame-Options is configured to DENY. This option is required to be set to SAMEORIGIN or ALLOW-FORM <X-Cart host> for X-Cart installation process. You can set it back to DENY after X-Cart is installed.
                [],
            ];
        };
    }

    /**
     * @return \Closure
     */
    private function getLoopbackRequestChecker()
    {
        return function () {
            $result = true;

            if (
                function_exists('curl_init')
                && function_exists('curl_version')
                && isset($_SERVER['HTTP_HOST'])
                && 'cli' !== PHP_SAPI
            ) {
                $adminScript = \Includes\Utils\FileManager::isFileReadable($this->getAdminScript())
                    ? $this->getAdminScript()
                    : 'admin.php';

                $customerScript = \Includes\Utils\FileManager::isFileReadable($this->getCustomerScript())
                    ? $this->getCustomerScript()
                    : 'cart.php';

                $url = 'http' . (\Includes\Utils\URLManager::isHTTPS() ? 's' : '') . '://' . $_SERVER['HTTP_HOST']
                    . (str_replace(['install.php', $customerScript, $adminScript], 'service.php', $_SERVER['PHP_SELF']));

                $curl = curl_init();

                curl_setopt_array(
                    $curl,
                    [
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_URL            => $url,
                        CURLOPT_HEADER         => 1,
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/999.99 (KHTML, like Gecko) Chrome/99.0.9999.99 Safari/999.99',
                    ]
                );

                if (
                    ($authUser = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_user']))
                    && ($authPass = \Includes\Utils\ConfigParser::getOptions(['service', 'basic_auth_pass']))
                ) {
                    curl_setopt($curl, CURLOPT_USERPWD, $authUser . ':' . $authPass);
                }

                $resp = curl_exec($curl);

                $result = preg_match('/<div id=app><\/div>/i', $resp);

                curl_close($curl);
            }

            return [
                $result,
                'error_message_1',
                ['url' => 'https://www.x-cart.com/contact-us.htmlutm_source=XC5Install&amp;utm_medium=reqsFailure&amp;utm_campaign=XC5Install'],
            ];
        };
    }

    /**
     * @return string
     */
    private function getAdminScript()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('host_details', 'admin_self'));
    }

    /**
     * @return string
     */
    private function getCustomerScript()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('host_details', 'cart_self'));
    }

    // }}}

    // {{{ Environment

    /**
     * @param $name
     *
     * @return mixed
     */
    private function getEnvironment($name)
    {
        return isset($this->environment[$name]) ? $this->environment[$name] : '';
    }

    // }}}

    // {{{ Check requirements

    /**
     * @param array $exclude
     */
    private function check(array $exclude = [])
    {
        foreach ($this->requirements as $name => $requirement) {
            if (in_array($name, $exclude, true)) {
                continue;
            }

            $this->checkRequirement($name);
        }

        $this->checked = true;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    private function checkRequirement($name)
    {
        $result = false;

        if ($this->hasRequirement($name)) {
            if ($this->isChecked($name)) {
                $result = $this->isSuccess($name);

            } else {
                if ($this->checkDependency($name)) {
                    $result = $this->runChecker($name) || $this->runFixer($name);

                } else {
                    $this->setFailureByDependency($name);

                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    private function checkDependency($name)
    {
        $result = true;
        foreach ($this->getDependency($name) as $dependency) {
            if (!$this->checkRequirement($dependency)) {
                $result = false;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Requirements management

    /**
     * @param string $name
     *
     * @return boolean
     */
    private function hasRequirement($name)
    {
        return isset($this->requirements[$name]);
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    private function getRequirement($name)
    {
        return isset($this->requirements[$name]) ? $this->requirements[$name] : null;
    }

    /**
     * @param string $name
     * @param array  $data
     */
    private function mergeRequirement($name, $data)
    {
        $requirement = $this->getRequirement($name);
        if ($requirement) {
            $this->requirements[$name] = array_merge($requirement, $data);
        }
    }

    /**
     * @param string $name
     *
     * @return array
     */
    private function getDependency($name)
    {
        $requirement = $this->getRequirement($name);

        return isset($requirement['dependency']) && is_array($requirement['dependency'])
            ? $requirement['dependency']
            : [];
    }

    /**
     * @param $name
     *
     * @return boolean
     */
    private function isChecked($name)
    {
        $requirement = $this->getRequirement($name);

        return $requirement ? (bool) ($requirement['state'] & self::STATE_CHECKED) : false;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    private function isSuccess($name)
    {
        $requirement = $this->getRequirement($name);

        return $requirement ? (bool) ($requirement['state'] & self::STATE_SUCCESS) : false;
    }

    /**
     * @param string $name
     */
    private function setSuccess($name)
    {
        $this->mergeRequirement($name, ['state' => self::STATE_CHECKED | self::STATE_SUCCESS]);
    }

    /**
     * @param string $name
     */
    private function setFailure($name)
    {
        $this->mergeRequirement($name, ['state' => self::STATE_CHECKED | self::STATE_FAILURE]);
    }

    /**
     * @param string $name
     */
    private function setFailureByDependency($name)
    {
        $this->mergeRequirement($name, ['state' => self::STATE_CHECKED | self::STATE_SKIPPED]);
    }

    /**
     * @param string $name
     * @param array  $data
     */
    private function setData($name, $data)
    {
        $this->mergeRequirement($name, ['data' => $data]);
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    private function runChecker($name)
    {
        $requirement = $this->getRequirement($name);
        if ($requirement && isset($requirement['checker']) && is_callable($requirement['checker'])) {
            $result = $requirement['checker']();
            list($result, $description, $data) = is_array($result) ? $result : [$result, '', []];

            $this->mergeRequirement(
                $name,
                [
                    'description' => $description,
                    'data'        => $data,
                ]
            );

            if ($result) {
                $this->setSuccess($name);

                return true;

            } else {
                $this->setFailure($name);

                return false;
            }
        }

        // todo: report run failure
        return false;
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    private function runFixer($name)
    {
        $requirement = $this->getRequirement($name);
        if ($requirement && isset($requirement['fixer']) && is_callable($requirement['fixer'])) {
            $result = $requirement['fixer']();
            list($result, $description, $data) = is_array($result) ? $result : [$result, []];

            $this->mergeRequirement(
                $name,
                [
                    'description' => $description,
                    'data'        => $data,
                ]
            );

            if ($result) {
                $this->setSuccess($name);

                return true;

            } else {
                $this->setFailure($name);

                return false;
            }
        }

        // todo: report run failure
        return false;
    }

    // }}}

    // {{{ Helpers

    /**
     * @return array
     */
    private function getPhpInfo()
    {
        if ($this->phpInfo === null) {
            $this->phpInfo = $this->definePhpInfo();
        }

        return $this->phpInfo;
    }

    /**
     * @return array
     */
    private function definePhpInfo()
    {
        $result = [];

        ob_start();
        phpinfo();
        $rawPhpInfo = ob_get_contents();
        ob_end_clean();

        foreach (explode("\n", $rawPhpInfo) as $line) {

            if (stripos($line, 'command') !== false) {
                $result['commands_exists'] = true; // wtf?
                if (stripos($line, '--enable-memory-limit') !== false) {
                    $result['no_mem_limit'] = false;
                }

                continue;
            }

            if (preg_match('/Phar EXT version.*<\/td><td([^>]*)>([^<]*)/i', $line, $match)) {
                $result['phar_ext_ver'] = $match[2];
            }
        }

        return array_merge(
            [
                'thread_safe'            => false,
                'debug_build'            => false,
                'php_ini_path'           => '',
                'no_mem_limit'           => true,
                'commands_exists'        => false,
                'php_ini_path_forbidden' => false,
                'phar_ext_ver'           => '',
            ],
            $result
        );
    }

    /**
     * @return array
     */
    private static function getFilePermissionsPaths()
    {
        return [
            'var',
            'images',
            'files',
            'etc' . DIRECTORY_SEPARATOR . 'config.php',
            '.htaccess',
        ];
    }

    /**
     * @return array
     */
    private static function getRequiredFunctions()
    {
        return [
            'func_num_args', 'func_get_arg', 'func_get_args', 'strlen',
            'strcmp', 'strncmp', 'strcasecmp', 'strncasecmp',
            'each', 'error_reporting', 'define', 'defined',
            'get_class', 'get_called_class', 'get_parent_class', 'method_exists',
            'property_exists', 'class_exists', 'interface_exists', 'function_exists',
            'get_included_files', 'is_subclass_of', 'is_a', 'get_class_vars',
            'get_object_vars', 'set_error_handler', 'restore_error_handler', 'set_exception_handler',
            'get_declared_classes', 'get_resource_type', 'extension_loaded', 'debug_backtrace',
            'debug_print_backtrace', 'strtotime', 'date', 'gmdate',
            'mktime', 'strftime', 'time', 'getdate',
            'date_create', 'date_default_timezone_set', 'date_default_timezone_get',
            'preg_match', 'preg_match_all', 'preg_replace', 'preg_replace_callback',
            'preg_split', 'preg_quote', 'preg_grep', 'preg_last_error',
            'ctype_alpha', 'ctype_digit',
            'filter_var', 'filter_var_array', 'hash_hmac', 'json_encode',
            'json_decode',
            'spl_autoload_register', 'spl_autoload_unregister', 'spl_autoload_functions',
            'class_parents', 'class_implements', 'spl_object_hash', 'iterator_to_array',
            'simplexml_load_file', 'constant',
            'sleep', 'flush', 'htmlspecialchars', 'htmlentities',
            'html_entity_decode', 'get_html_translation_table', 'sha1', 'md5',
            'md5_file', 'crc32', 'getimagesize', 'phpinfo',
            'phpversion', 'substr_count', 'strspn', 'strcspn',
            'strtok', 'strtoupper', 'strtolower', 'strpos',
            'stripos', 'strrpos', 'strrev', 'nl2br',
            'basename', 'dirname', 'pathinfo', 'stripslashes',
            'stripcslashes', 'strstr', 'stristr', 'str_split',
            'substr', 'substr_replace', 'ucfirst', 'lcfirst',
            'ucwords', 'strtr', 'addslashes', 'addcslashes',
            'rtrim', 'str_replace', 'str_ireplace', 'str_repeat',
            'chunk_split', 'trim', 'ltrim', 'strip_tags',
            'explode', 'implode', 'join', 'setlocale',
            'chr', 'ord', 'parse_str', 'str_pad',
            'chop', 'sprintf', 'printf', 'sscanf',
            'parse_url', 'urlencode', 'urldecode', 'http_build_query',
            'unlink', 'exec', 'escapeshellcmd', 'escapeshellarg',
            'rand', 'srand', 'mt_rand', 'mt_srand',
            'getmypid', 'base64_encode', 'abs', 'ceil',
            'floor', 'round', 'is_infinite', 'pow',
            'log', 'sqrt', 'hexdec', 'octdec',
            'dechex', 'base_convert', 'number_format', 'getenv',
            'putenv', 'microtime', 'uniqid', 'quoted_printable_encode',
            'set_time_limit', 'get_magic_quotes_gpc', 'get_magic_quotes_runtime',
            'error_log', 'error_get_last', 'call_user_func', 'call_user_func_array',
            'serialize', 'unserialize', 'var_dump', 'var_export',
            'print_r', 'memory_get_usage', 'memory_get_peak_usage', 'register_shutdown_function',
            'ini_get', 'ini_set', 'get_include_path', 'set_include_path', 'setcookie',
            'header', 'headers_sent', 'parse_ini_file', 'is_uploaded_file',
            'move_uploaded_file', 'intval', 'floatval', 'doubleval',
            'strval', 'gettype', 'is_null', 'is_resource',
            'is_bool', 'is_float', 'is_int', 'is_integer',
            'is_numeric', 'is_string', 'is_array', 'is_object',
            'is_scalar', 'is_callable', 'pclose', 'popen',
            'readfile', 'rewind', 'rmdir', 'umask',
            'fclose', 'feof', 'fgets', 'fread',
            'fopen', 'fstat', 'fflush', 'fwrite',
            'fputs', 'mkdir', 'rename', 'copy',
            'tempnam', 'file', 'file_get_contents', 'file_put_contents',
            'stream_context_create', 'stream_context_set_params', 'stream_filter_append', 'stream_filter_remove',
            'stream_socket_enable_crypto', 'stream_get_contents', 'flock', 'stream_get_meta_data',
            'stream_set_timeout', 'socket_set_timeout', 'socket_get_status', 'realpath',
            'fsockopen', 'pack', 'unpack', 'opendir',
            'closedir', 'chdir', 'getcwd', 'readdir',
            'glob', 'filemtime', 'fileperms', 'filesize',
            'file_exists', 'is_writable', 'is_readable', 'is_executable',
            'is_file', 'is_dir', 'is_link', 'chmod',
            'touch', 'clearstatcache', 'disk_free_space', 'mail',
            'openlog', 'syslog', 'closelog', 'ob_start',
            'ob_flush', 'ob_clean', 'ob_end_clean', 'ob_get_clean',
            'ob_get_contents', 'ksort', 'krsort', 'asort',
            'sort', 'usort', 'uasort', 'uksort',
            'array_walk', 'array_walk_recursive', 'count', 'end',
            'next', 'reset', 'current', 'key',
            'min', 'max', 'in_array', 'array_search',
            'compact', 'array_fill', 'array_fill_keys', 'range',
            'array_multisort', 'array_push', 'array_pop', 'array_shift',
            'array_unshift', 'array_splice', 'array_slice', 'array_merge',
            'array_merge_recursive', 'array_replace_recursive', 'array_keys', 'array_values',
            'array_count_values', 'array_reverse', 'array_reduce', 'array_pad',
            'array_flip', 'array_change_key_case', 'array_unique', 'array_intersect',
            'array_intersect_key', 'array_diff', 'array_diff_key', 'array_diff_assoc',
            'array_udiff_assoc', 'array_sum', 'array_filter', 'array_map',
            'array_chunk', 'array_combine', 'array_key_exists', 'version_compare',
            'stream_get_filters', 'sys_get_temp_dir', 'token_get_all', 'xml_parser_create',
            'xml_parse_into_struct', 'xml_get_error_code', 'xml_error_string', 'xml_get_current_byte_index',
            'xml_parser_free',
        ];
    }

    /**
     * @param boolean $force
     *
     * @return array
     */
    private function getConfig($force = false)
    {
        if ($force || $this->config === null) {
            $this->config = $this->readConfig();
        }

        return $this->config;
    }

    /**
     * @return array
     */
    private function readConfig()
    {
        $result = [];

        /** @var string $configPath */
        $configPath = $this->getEnvironment('rootPath') . 'etc' . DIRECTORY_SEPARATOR;
        /** @var string[] $configFiles */
        $configFiles = [
            $this->getEnvironment('defaultConfigFileName'),
            $this->getEnvironment('configFileName'),
        ];

        foreach ($configFiles as $configFile) {
            if (file_exists($configPath . $configFile)) {
                $data = @parse_ini_file($configPath . $configFile, true);
                if (!empty($data) && is_array($data)) {
                    $result[] = $data;
                }
            }
        }

        return count($result) > 1 ? call_user_func_array('array_replace_recursive', $result) : $result;
    }

    /**
     * @param boolean $force
     *
     * @return \PDO
     */
    private function getPDO($force = false)
    {
        if ($force || $this->pdo === null) {
            $this->pdo = $this->definePDO();
        }

        return $this->pdo;
    }

    /**
     * @return \PDO
     */
    private function definePDO()
    {
        $options = $this->getEnvironment('databaseDetails') ?: [];

        $dsnFields = [
            'host'        => 'hostspec',
            'port'        => 'port',
            'unix_socket' => 'socket',
            'dbname'      => 'database',
        ];

        foreach ($dsnFields as $pdoOption => $lcOption) {
            if (!empty($options[$lcOption])) {
                $dsnFields[$pdoOption] = $options[$lcOption];
            } else {
                unset($dsnFields[$pdoOption]);
            }
        }

        $dsnParts = [];
        foreach ($dsnFields as $name => $value) {
            $dsnParts[] = $name . '=' . $value;
        }
        $dsn = 'mysql:' . implode(';', $dsnParts);

        $username = isset($options['username']) ? $options['username'] : '';
        $password = isset($options['password']) ? $options['password'] : '';
        $options  = [
            \PDO::ATTR_AUTOCOMMIT => true,
            \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_PERSISTENT => false,
        ];

        try {
            return new \PDO(
                $dsn,
                $username,
                $password,
                $options
            );
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return boolean
     */
    private function isMariaDB($version) {
        return strripos($version, 'MariaDB');
    }

    // }}}
}
