<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * X-Cart installation procedures
 */

if (!defined('XLITE_INSTALL_MODE')) {
    die('Incorrect call of the script. Stopping.');
}

/**
 * Returns a processed text by specified label value
 *
 * @param $label string Label value
 * @param $substitute array Array for substitution parameters in the text found by label
 *
 * @return string
 */
function xtr($label, array $substitute = array())
{
    $text = getTextByLabel($label);

    if (!empty($substitute)) {
        uksort($substitute, function($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($substitute as $key => $value) {
            if (is_scalar($value)) {
                $text = str_replace($key, $value, $text);
            }
        }
    }

    return $text;
}

/**
 * Returns a text by label. If label not found in translation file then label itself will be returned
 *
 * @param $label string Label value
 *
 * @return string
 */
function getTextByLabel($label)
{
    $result = $label;

    static $translation;

    if (!isset($translation)) {

        // Get language code from cookies...
        if (isset($_COOKIE['lang']) && !empty($_COOKIE['lang'])) {
            $languageCode = $_COOKIE['lang'];

        // or from main installation settings
        } else {
            global $lcSettings;
            $languageCode = $lcSettings['default_language_code'];
        }

        // Check if language code value is satisfied to alpha-2 pattern for security reasons
        if (!preg_match('/^[a-z]{2}$/', $languageCode)) {
            $languageCode = 'en';
        }

        // Generate name of file that should contain language variables
        $labelsFile = constant('LC_DIR_ROOT') . 'Includes/install/translations/' . $languageCode . '.php';

        // Check if this file exists and include it (it must be correct php script, that is contained $translation array) otherwise include default translations(english)
        if (!file_exists($labelsFile)) {
            $labelsFile = constant('LC_DIR_ROOT') . 'Includes/install/translations/' . 'en.php';
        }
        include_once $labelsFile;
    }

    // Check if label value defined in translation array and assign this as a result
    if (!empty($translation[$label])) {
        $result = $translation[$label];
    }

    return $result;
}

/**
 * Logging functions
 */

/**
 * Write a record to log
 *
 * @param $message string The log message
 *
 * @return void
 */

function x_install_log($message = null)
{
    $fileName =  LC_DIR_VAR . 'log' . LC_DS . 'install_log.' . date('Y-m-d') . '.php';
    $securityHeader = "<?php die(1); ?>\n";

    if (!file_exists($fileName) || $securityHeader > filesize($fileName)) {
        @file_put_contents($fileName, $securityHeader);
    }

    $args = func_get_args();

    $message = array_shift($args);

    if (empty($message)) {
        $message = 'Debug info';
    }

    $currentDate = date(DATE_RFC822);

    $host = x_install_get_host($_SERVER['HTTP_HOST']);

    $port = $_SERVER['SERVER_PORT'] ? ':' . $_SERVER['SERVER_PORT'] : '';

    $protocol = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https' : 'http';

    $softwareVersion = LC_VERSION;

    $output =<<< OUT


--------------------------------------------------------------------
[$currentDate]
[X-Cart v{$softwareVersion}]
[{$_SERVER['REQUEST_METHOD']}, {$_SERVER['SERVER_PROTOCOL']}] {$protocol}://{$host}{$port}{$_SERVER['REQUEST_URI']}
[{$_SERVER['SERVER_SOFTWARE']}]
$message

OUT;

    if (!empty($args)) {

        $args = x_install_log_mask_params($args);

        ob_start();

        foreach ($args as $value) {
            var_export($value);
        }

        $varDump = ob_get_contents();
        ob_end_clean();

        $output .= $varDump;
    }

    @file_put_contents($fileName, $output, FILE_APPEND);
}

/**
 * Mask some private data in the $params array to avoid this to be passeded to the log
 *
 * @param array $params An array $_POST or $_GET
 *
 * @return array
 */
function x_install_log_mask_params($params)
{
    static $fieldsToMask = array(
        'auth_code',
        'mysqlpass',
        'password',
        'pass1',
        'pass2',
    );

    static $fieldsToHide = array(
        '_login',
        '_password'
    );

    foreach ($params as $key => $value) {
        if (is_array($value)) {
            $params[$key] = x_install_log_mask_params($value);

        } elseif (in_array($key, $fieldsToMask, true)) {
            $params[$key] = empty($value) ? '<empty>' : '<specified>';

        } elseif (in_array($key, $fieldsToHide, true)) {
            unset($params[$key]);
        }
    }

    return $params;
}

/**
 * Checking requirements section
 */

/**
 * Return true if failed requirement with specified code is a error which is removable hard
 *
 * @param string $code Code
 *
 * @return boolean
 */
function isHardError($code)
{
    return in_array(
        $code,
        array(
            'php_version',
            'mysql_version',
            'doc_blocks_support',
            'php_pdo_mysql',
            'php_memory_limit',
            'config_file',
            'file_permissions',
            'frame_options'
        )
    );
}

/**
 * Perform the requirements checking
 *
 * @return array
 */
function doCheckRequirements($environment = array())
{
    $passed = true;
    $result = [];
    $exclude = [];

    $requirements = new \Includes\Requirements($environment);
    foreach ($requirements->getResult($exclude) as $name => $requirement) {
        if ($name === 'install_script') {
            continue;
        }

        $status = $requirement['state'] === \Includes\Requirements::STATE_SUCCESS;
        $passed = $passed && $status;

        $data = isset($requirement['data']) ? $requirement['data'] : '';
        $value = '';

        switch ($name) {
            case 'php_version':
                $value = isset($data['version']) ? $data['version'] : '';
                break;
            case 'php_disabled_functions':
                if (isset($data['missed'])) {
                    $value = substr(implode(', ', $data['missed']), 0, 45) . '...';
                    foreach ($data['missed'] as $function) {
                        ga_event('warning', 'disabled_function', $function);
                    }
                } else {
                    $value = 'none';
                }
                break;
            case 'php_memory_limit':
                $value = isset($data['memoryLimit']) ? $data['memoryLimit'] : '';
                break;
            case 'php_file_uploads':
                $value = isset($data['file_uploads']) ? $data['file_uploads'] : '';
                break;
            case 'php_upload_max_file_size':
                $value = isset($data['upload_max_filesize']) ? $data['upload_max_filesize'] : '';
                break;
            case 'file_permissions':
                break;
            case 'mysql_version':
                $value = isset($data['version']) ? $data['version'] : '';
                if ($value && 'unknown' != $value) {
                    global $params;
                    $params['mysqlVersion'] = $mysqlVersion = $value;
                }
                break;
            case 'php_gdlib':
                $value = isset($data['version']) ? $data['version'] : '';
                break;
            case 'php_phar':
                $value = isset($data['version']) ? $data['version'] : '';
                break;
            case 'https_bouncer':
                $value = isset($data['version']) ? $data['version'] : '';
                break;
            case 'xml_support':
                $value = isset($data['extensions']) ? implode(', ', $data['extensions']) : '';
                break;
        }

        $resultRequirement = [
            'title' => xtr($requirement['title']),
            'status' => $status,
            'critical' => $requirement['level'] === \Includes\Requirements::LEVEL_CRITICAL,
            'value' => $value,
            'data' => $requirement['data'],
        ];

        $messageData = [];
        foreach ($requirement['data'] as $varName => $varValue) {
            if (is_scalar($varValue)) {
                $messageData[':' . $varName] = $varValue;
            }
        }
        $resultRequirement['messageData'] = $messageData;

        $resultRequirement['description'] = $status ? '' : xtr($name .  '.' . $requirement['description'], $messageData);

        if ($requirement['state'] === \Includes\Requirements::STATE_SKIPPED) {
            $resultRequirement['skipped'] = true;
        }

        $result[$name] = $resultRequirement;
    }

    if ($passed) {
        x_install_log(xtr('Checking requirements is successfully complete'));
        x_install_log(xtr('Requirements log'), $result);
    }

    return $result;
}

/**
 * End of Checking requirements section
 */

/**
 * Prepare the fixtures: the list of yaml files for uploading to the database
 *
 * @param array  $params     Database access data and other parameters
 * @param bool   $silentMode Do not display any output during installing
 *
 * @return bool
 */
function doPrepareFixtures(&$params, $silentMode = false)
{
    global $lcSettings;

    $result = true;

    if (!$silentMode) {
        echo '<div class="process-iframe-header">' . xtr('Preparing data for cache generation...');
    }

    $enabledModules = array();
    $moduleYamlFiles = array();

    foreach ((array) glob(LC_DIR_MODULES . '*', GLOB_ONLYDIR) as $authorDir) {

        $author = basename($authorDir);

        foreach ((array) glob(LC_DIR_MODULES . $author . '/*/main.yaml') as $f) {

            $moduleName = basename(dirname($f));

            $enabledModules[$author][$moduleName] = intval(
                !empty($lcSettings['enable_modules'][$author])
                && in_array($moduleName, $lcSettings['enable_modules'][$author])
            );

            if (!$enabledModules[$author][$moduleName]) {
                continue;
            }

            $dir = 'classes' . LC_DS
                . 'XLite' . LC_DS
                . 'Module' . LC_DS
                . $author . LC_DS
                . $moduleName;

            $moduleFile = $dir . LC_DS . 'install.yaml';
            if (file_exists(LC_DIR_ROOT . $moduleFile)) {
                $moduleYamlFiles[] = $moduleFile;
            }

            foreach ((array) glob(LC_DIR_ROOT . $dir . LC_DIR . 'install_*.yaml') as $translationFile) {
                if (
                    file_exists(LC_DIR_ROOT . $translationFile)
                    && is_file(LC_DIR_ROOT . $translationFile)
                ) {
                    $moduleYamlFiles[] = $translationFile;
                }
            }
        }
    }

    sort($moduleYamlFiles, SORT_STRING);

    \Includes\Utils\Module\Manager::getRegistry()->clear();
    \Includes\Utils\Module\Manager::saveModulesToStorage($enabledModules);

    // Generate fixtures list
    $yamlFiles = $lcSettings['yaml_files']['base'];

    foreach ($moduleYamlFiles as $f) {
        // Add module fixtures
        $yamlFiles[] = $f;
    }

    if (!empty($params['demo'])) {
        // Add demo dump to the fixtures
        foreach ($lcSettings['yaml_files']['demo'] as $f) {
            $yamlFiles[] = $f;
        }
    }

    foreach ($lcSettings['yaml_files']['base_after'] as $f) {
        $yamlFiles[] = $f;
    }

    // Remove fixtures file (if exists)
    \Includes\Decorator\Plugin\Doctrine\Utils\FixturesManager::removeFixtures();

    // Add fixtures list
    foreach ($yamlFiles as $file) {
        \Includes\Decorator\Plugin\Doctrine\Utils\FixturesManager::addFixtureToList($file);
    }

    if (!$silentMode) {
        echo status($result) . '</div>';
    }

    return $result;
}

function doUpdateConfig(&$params, $silentMode = false)
{
    // Update etc/config.php file
    $configUpdated = false;

    if (!$silentMode) {
        echo '<br /><b>' . xtr('Updating config file...') . '</b><br>';
    }

    $isConnected = dbConnect($params, $pdoErrorMsg);

    if ($isConnected) {

        // Write parameters into the config file
        if (@is_writable(LC_DIR_CONFIG . constant('LC_CONFIG_FILE'))) {
            $configUpdated = change_config($params);

        } else {
            $configUpdated = false;
        }

        if (true !== $configUpdated && !$silentMode) {
            fatal_error(xtr('config_writing_error', array(':configfile' => constant('LC_CONFIG_FILE'))), 'file', 'config_writing_error');
        }

    } elseif (!$silentMode) {
        fatal_error(xtr('mysql_connection_error', array(':pdoerr' => (!empty($pdoErrorMsg) ? ': ' . $pdoErrorMsg : ''))), 'pdo', @$pdoErrorMsg);
    }

    return $configUpdated;
}

/**
 * Modify main .htaccess file
 *
 * @param array   &$params    Database access data and other parameters
 * @param boolean $silentMode Flag OPTIONAL
 *
 * @return boolean
 */
function doUpdateMainHtaccess(&$params, $silentMode = false)
{
    if (!empty($params['xlite_web_dir'])) {

        if (!$silentMode) {
            echo '<div class="process-iframe-header">' . xtr('Updating .htaccess...') . '</div>';
        }

        $util = '\Includes\Utils\FileManager';

        $util::replace(
            $util::getDir($util::getDir(__DIR__)) . LC_DS . '.htaccess',
            '\1RewriteBase ' . $params['xlite_web_dir'],
            '/^(\s*)#\s*RewriteBase\s+____WEB_DIR____\s*$/mi'
        );

        $util::replace(
            $util::getDir($util::getDir(__DIR__)) . LC_DS . '.htaccess',
            '\1ErrorDocument 404 ' . $params['xlite_web_dir'],
            '/^(\s*)#ErrorDocument\s+404\s+____WEB_DIR____/mi'
        );
    }

    return true;
}

/**
 * Prepare to remove a cache of classes
 *
 * @param array $params Database access data and other parameters
 *
 * @return bool
 */
function doRemoveCache($params)
{
    doPrepareCacheRemoval();

    return doDropDatabaseTables($params);
}

/**
 * Prepare to remove cache of classes
 *
 * @return void
 */
function doPrepareCacheRemoval()
{
    \Includes\Decorator\Utils\CacheManager::cleanupCacheIndicators();

    // Remove disabled structures
    $paths = array(
        LC_DIR_SERVICE . '.disabled.structures.php',
        LC_DIR_SERVICE . '.modules.structures.registry.hash.php',
        LC_DIR_SERVICE . '.modules.structures.registry.php',
    );

    foreach ($paths as $path) {
        if (file_exists($path)) {
            file_put_contents($path, '');
        }
    }
}

/**
 * Drop X-Cart database tables
 * Return true on success
 *
 * @param array $params
 *
 * @return boolean
 */
function doDropDatabaseTables($params)
{
    $result = true;
    $pdoErrorMsg = '';

    // Remove all X-Cart tables if exists
    $connection = dbConnect($params, $pdoErrorMsg);

    if ($connection) {

        // Check if X-Cart tables already exist
        $res = dbFetchAll('SHOW TABLES LIKE \'' . str_replace('_', '\\_', get_db_tables_prefix()) . '%\'');

        if (is_array($res)) {

            dbExecute('SET FOREIGN_KEY_CHECKS=0', $pdoErrorMsg);

            foreach ($res as $row) {
                $tableName = array_pop($row);
                $pdoErrorMsg = '';

                $_query = sprintf('DROP TABLE `%s`', $tableName);
                dbExecute($_query, $pdoErrorMsg);

                if (!empty($pdoErrorMsg)) {
                    $result = false;
                    break;
                }
            }

            $pdoErrorMsg2 = '';
            dbExecute('SET FOREIGN_KEY_CHECKS=1', $pdoErrorMsg2);

            if (empty($pdoErrorMsg)) {
                $pdoErrorMsg = $pdoErrorMsg2;
            }
        }

    } else {
        $result = false;
    }

    if (!$result) {
        x_install_log(xtr('doDropDatabaseTables() failed'), $pdoErrorMsg);
    }

    return $result;
}

/**
 * Generate a cache of classes
 *
 * @return bool
 */
function doBuildCache()
{
    $result = true;

    x_install_log(xtr('Cache building...'));

    ob_start();

    try {
        define('DO_ONE_STEP_ONLY', true);
        \Includes\Decorator\Utils\CacheManager::rebuildCache();

    } catch (\Exception $e) {
        x_install_log('Exception: ' . $e->getMessage());
        $result = false;
    }

    $message = ob_get_contents();
    ob_end_clean();

    if (!$result) {
        x_install_log(xtr('Cache building procedure failed: :message', array(':message' => $e->getMessage())));
    }

    return $result;
}

/**
 * Create required directories and files
 *
 * @param boolean $silentMode Do not display any output during installing
 *
 * @return boolean
 */
function doInstallDirs($silentMode = false)
{
    global $error, $lcSettings;

    $result = true;

    if ($silentMode) {
        ob_start();
    }

    if ($result && !empty($lcSettings['writable_directories'])) {
        echo '<div class="iframe-div">' . xtr('Checking directories permissions...') . '</div>';
        chmod_others_directories($lcSettings['writable_directories']);
    }

    if (!empty($lcSettings['directories_to_create'])) {
        echo '<div class="iframe-div">' . xtr('Creating directories...') . '</div>';
        $result = create_dirs($lcSettings['directories_to_create']);
    }

    if ($result) {
        echo '<div class="iframe-div">Creating directories process is finished</div>';
    }

    if ($silentMode) {

        if (!$result) {
            $output = ob_get_contents();
        }

        ob_end_clean();

    } else {

        if (!$result) {
            fatal_error(xtr('fatal_error_creating_dirs'), 'file', 'creating_dirs');
        }
    }

    return $result;
}

/**
 * Create an administrator account
 *
 * @param array  $params     Database access data and other parameters
 * @param bool   $silentMode Do not display any output during installing
 *
 * @return bool
 */
function doCreateAdminAccount(&$params, $silentMode = false)
{
    global $error;

    $result = true;

    if ($silentMode) {
        ob_start();
    }

    $login = $params['login'];
    $password = $params["password"];

    if (empty($login) || empty($password)) {
        $result = false;
        $errorMsg = fatal_error(xtr('Login and password can\'t be empty.'), 'params', 'empty admin login or password');

    } else {
        $password = md5($password);
    }

    $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($login);

    if (is_null($profile)) {
        // Register default admin account

        $profile = new \XLite\Model\Profile();
        $profile->setLogin($login);

        //echo xtr('Registering primary administrator profile...');

    } else {
        // Account already exists
        //echo xtr('Updating primary administrator profile...');
    }

    // Add banner for Paypal express checkout on the admin dashboard
    if (
        !in_array(XLITE_EDITION_LNG, ['ru', 'zh'])
        && class_exists('\XLite\Module\CDev\Paypal\Main')
    ) {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\Paypal',
                'name'     => 'show_admin_welcome',
                'value'    => 'Y',
            )
        );
    }

    $profile->setPassword($password);
    $profile->setAccessLevel(100);
    $profile->enable();

    $role = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneRoot();

    $profile->addRoles($role);

    $profile->create();

    $role->addProfiles($profile);
    \XLite\Core\Database::getEM()->persist($role);
    \XLite\Core\Database::getEM()->flush();

    if ($silentMode) {
        ob_end_clean();
    }

    return $result;
}

/**
 * Do some final actions
 *
 * @param array  $params     Database access data and other parameters
 * @param bool   $silentMode Do not display any output during installing
 *
 * @return bool
 */
function doFinishInstallation(&$params, $silentMode = false)
{
    global $lcSettings, $error;

    $result = true;

    // Update config settings
    update_config_settings($params);

    updateSystemDataStorage($params);

    // Save authcode for the further install runs
    $authcode = save_authcode($params);

    $install_name = rename_install_script();

    if ($install_name ) {

        // Text for email notification
        $install_rename_email = xtr('script_renamed_text',
            array(
                ':newname'  => $install_name,
                ':host'     => $params['xlite_http_host'],
                ':webdir'   => (isset($params['xlite_web_dir']) ? $params['xlite_web_dir'] : '')
            )
        );

        // Text for confirmation web page
        $install_rename = xtr('script_renamed_text_html', array(':newname' => $install_name));

    } else {
        $install_rename = xtr('script_cannot_be_renamed_text');
        $install_rename_email = strip_tags($install_rename);
    }

    // Prepare files permissions recommendation text
    $perms = '';
    $perms_no_tags = '';

    if (!LC_OS_IS_WIN) {

        $_perms = array();

        if (@is_writable(LC_DIR_ROOT)) {
            $_perms[] = 'chmod 755 ' . LC_DIR_ROOT;
        }

        if (@is_writable(LC_DIR_CONFIG)) {
            $_perms[] = 'chmod 755 ' . LC_DIR_CONFIG;
        }

        if (@is_writable(LC_DIR_CONFIG . constant('LC_CONFIG_FILE'))) {
            $_perms[] = "chmod 644 " . LC_DIR_CONFIG . constant('LC_CONFIG_FILE');
        }

        if (!empty($_perms)) {
            $perms = '<li>' . implode("</li>\n<li>", $_perms) . '</li>';
            $permsTitle = xtr('correct_permissions_text');
            $copyText = xtr('Copy to clipboard');
            $perms2Copy = strip_tags($perms);

            $permsHTML =<<<OUT

<div>{$permsTitle}</div>

<ul class="permissions-list">
$perms
</ul>

<div class="clipbrd">
  <input type="button" class="btn btn-default copy2clipboard" value="{$copyText}" />
  <div class="copy2clipboard-alert alert-success" style="display: none;"></div>
  <div class="permissions-list" style="display: none;">{$perms2Copy}</div>
</div>
OUT;

            $perms_no_tags = $permsTitle . "\n\n" . strip_tags($perms);
        }
    }

    // Prepare email notification text

    $message = xtr(
        'congratulations_text',
        array(
            ':host'         => $params['xlite_http_host'],
            ':webdir'       => (isset($params['xlite_web_dir']) ? $params['xlite_web_dir'] : ''),
            ':login'        => $params['login'],
            ':password'     => $params['password'],
            ':perms'        => $perms_no_tags,
            ':renametext'   => $install_rename_email,
            ':authcode'     => $authcode,
            ':safekey'      => '',
        )
    );

    // Send email notification to the admin account email
    @mail($params["login"], xtr("X-Cart installation complete"), $message,
        "From: \"X-Cart software\" <" . $params["login"] . ">\r\n" .
        "X-Mailer: PHP");

    if (!$silentMode) {

?>

<script type="text/javascript">
  document.cookie = 'xcInstallComplete=1';
  document.cookie = 'xcInstallStarted=; expires=-1';
  document.cookie = 'passed_steps=; expires=-1';
</script>

<?php echo $permsHTML; ?>

<div class="second-title"><?php echo xtr('X-Cart software has been successfully installed and is now available at the following URLs:'); ?></div>

<p class="customer-link"><a href="cart.php" class="final-link" target="_blank"><?php echo xtr('Customer zone (front-end)'); ?></a></p>

<p class="admin-link"><a href="admin.php" class="final-link" target="_blank"><?php echo xtr('Administrator zone (backoffice)'); ?></a></p>

<p class="concierge-note"><?php echo xtr('X-Cart can track and store user actions to improve the UI/UX and merchant experience. You can stop user action tracking at any time by removing the Concierge module.'); ?></p>

<p>
<?php echo $install_rename; ?>
</p>

<p>
<?php echo xtr('Your auth code for running install.php in the future is:'); ?> <code><?php echo get_authcode(); ?></code>
<br />
<?php echo xtr('PLEASE WRITE THIS CODE DOWN IN CASE YOU ARE GOING TO REMOVE ":filename"', array(':filename' => $install_name)); ?>
</p>

<img src="//www.x-cart.com/img/spacer2.gif" width="1" height="1" alt="" />

<?php

    }

    // Do this to correct categories structure after loading all yaml files
    // Remove after fix of BUG-3674
    \XLite\Core\Database::getRepo('XLite\Model\Category')->correctCategoriesStructure();

    x_install_log(xtr('Installation complete'));

    return $result;
}

/*
 * Service functions section
 */


/**
 * Sanitize host value (remove port as some servers include it to HTTP_HOST variable)
 *
 * @param string $host Host value
 *
 * @return string
 */
function x_install_get_host($host)
{
    if (false !== strstr($host, ':')) {
        list($result, $port) = explode(':', $host);
        if (!$port || (80 != $port && 443 != $port)) {
            $result = $host;
        }

    } else {
        $result = $host;
    }

    return $result;
}

/**
 * Create directories
 *
 * @param array $dirs Array of directory names
 *
 * @return bool
 */
function create_dirs($dirs)
{
    $result = true;

    $failedDirs = array();

    $dir_permission = 0777;

    $data = parse_config();

    if(constant('LC_SUPHP_MODE') != 0) {
        $dir_permission = isset($data['privileged_permission_dir']) ? base_convert($data['privileged_permission_dir'], 8, 10) : 0711;

    } else {
        $dir_permission = isset($data['nonprivileged_permission_dir']) ? base_convert($data['nonprivileged_permission_dir'], 8, 10) : 0755;
    }

    foreach ($dirs as $val) {
        echo xtr('Creating directory: [:dirname] ... ', array(':dirname' => $val));

        if (!@file_exists(constant('LC_DIR_ROOT') . $val)) {
            $res = @mkdir(constant('LC_DIR_ROOT') . $val, $dir_permission);
            $result &= $res;
            $failedDirs[] = constant('LC_DIR_ROOT') . $val;
            echo status($res);

        } else {
            echo '<span class="status-already-exists">' . xtr('Already exists') . '</span>';
        }

        echo "<br />\n"; flush();
    }

    if (!$result) {
        x_install_log(xtr('Failed to create directories'), $failedDirs);
    }

    return $result;
}

/**
 * Set permissions on directories
 *
 * @param array $dirs Array of directory names
 *
 * @return void
 */
function chmod_others_directories($dirs)
{
    if (constant('LC_SUPHP_MODE') != 0) {
        $data = parse_config();
        $dir_permission = isset($data['privileged_permission_dir']) ? base_convert($data['privileged_permission_dir'], 8, 10) : 0711;

        foreach($dirs as $dir) {
            echo $dir . '... ';
            $result = @chmod(constant('LC_DIR_ROOT') . $dir, $dir_permission);
            echo status($result);
        }

    } else {
        echo status(true);
    }
}

/**
 * Check writable permissions for specified object (file or directory) recusrively
 *
 * @param string Object path
 *
 * @return array
 */
function checkPermissionsRecursive($object)
{
    $dirPermissions = '777';
    $filePermissions = '666';

    $result = array();

    if (is_dir($object)) {

        if (!is_writable($object)) {
            $result[$object] = $dirPermissions;

        } else {

            if ($handle = @opendir($object)) {

                while (($file = readdir($handle)) !== false) {

                    // Skip '.', '..', '.htaccess' and other files those names starts from '.'
                    if (preg_match('/^\./', $file)) {
                        continue;
                    }

                    $fileRealPath = $object . LC_DS . $file;

                    if (!is_writable($fileRealPath)) {
                        $result[$fileRealPath] = (is_dir($fileRealPath) ? $dirPermissions : $filePermissions);

                    } elseif (is_dir($fileRealPath)) {
                        $result = array_merge($result, checkPermissionsRecursive($fileRealPath));
                    }

                }

                closedir($handle);
            }
        }

    } elseif (!is_writable($object)) {
        $result[$object] = $filePermissions;
    }

    return $result;
}

/**
 * Function to copy directory tree from skins_original to skins
 *
 * @param string $source_dir      Source directory name
 * @param string $parent_dir      Parent directory name
 * @param string $destination_dir Destination directory name
 *
 * @return bool
 */
function copy_files($source_dir, $parent_dir, $destination_dir, &$failedList)
{
    $result = true;

    $dir_permission = 0777;

    if (constant('LC_SUPHP_MODE') != 0) {
        $data = parse_config();
        $dir_permission = isset($data['privileged_permission_dir']) ? base_convert($data['privileged_permission_dir'], 8, 10) : 0711;
    }

    if ($handle = @opendir(constant('LC_DIR_ROOT') . $source_dir)) {

        while (($file = readdir($handle)) !== false) {

            $sourceFile = constant('LC_DIR_ROOT') . $source_dir . '/' . $file;
            $destinationFile = constant('LC_DIR_ROOT') . $destination_dir . '/' . $parent_dir . '/' . $file;

            // .htaccess files must be already presented in the destination directory and they should't have writable permissions for web server user
            if (@is_file($sourceFile) && $file != '.htaccess') {

                if (!@copy($sourceFile, $destinationFile)) {
                    echo xtr(
                        'Copying: [:source_dir:parent_dir/:file] to [:destination_dir:parent_dir/:file]... ',
                        array(
                            ':source_dir' => $source_dir,
                            ':parent_dir' => $parent_dir,
                            ':file' => $file,
                            ':destination_dir' => $destination_dir,
                        )
                    )
                    . status(false) . "<BR>\n";
                    $result = false;
                    $failedList[] = sprintf('copy(%s, %s)', $sourceFile, $destinationFile);
                }

                flush();

            } elseif (@is_dir($sourceFile) && $file != '.' && $file != '..') {

                echo xtr(
                    'Creating directory: [:destination_dir:parent_dir/:file]... ',
                    array(
                        ':destination_dir' => $destination_dir,
                        ':parent_dir' => $parent_dir,
                        ':file' => $file,
                    )
                );

                if (!@file_exists($destinationFile)) {

                    if (!@mkdir($destinationFile, $dir_permission)) {
                        echo status(false);
                        $result = false;
                        $failedList[] = sprintf('mkdir(%s)', $destinationFile);

                    } else {
                        echo status(true);
                    }

                } else {
                    echo '<span class="status-already-exists">' . xtr('Already exists') . '</span>';
                }

                echo "<br />\n";

                flush();

                $result &= copy_files($source_dir . '/' . $file, $parent_dir . '/' . $file, $destination_dir, $failedList);
            }
        }

        closedir($handle);

    } else {
        echo status(false) . "<br />\n";
        $result = false;
        $failedList[] = sprintf('opendir(%s)', constant('LC_DIR_ROOT') . $source_dir);
    }

    return $result;
}

/**
 * Prepare content for writing to the config.php file
 *
 * @param array $params
 *
 * @return void
 */
function change_config(&$params)
{
    global $installation_auth_code;

    static $pairs = array(
        'hostspec'     => 'mysqlhost',
        'database'     => 'mysqlbase',
        'username'     => 'mysqluser',
        'password'     => 'mysqlpass',
        'table_prefix' => 'mysqlprefix',
        'charset'      => 'mysqlcharset',
        'port'         => 'mysqlport',
        'socket'       => 'mysqlsock',
        'http_host'    => 'xlite_http_host',
        'https_host'   => 'xlite_https_host',
        'web_dir'      => 'xlite_web_dir',
    );


    // check whether config file is writable
    clearstatcache();

    if (!@is_readable(LC_DIR_CONFIG . constant('LC_CONFIG_FILE')) || !@is_writable(LC_DIR_CONFIG . constant('LC_CONFIG_FILE'))) {
        return false;
    }

    // read file content
    if (!$config = file(LC_DIR_CONFIG . constant('LC_CONFIG_FILE'))) {
        return false;
    }

    $params['xlite_http_host'] = x_install_get_host($params['xlite_http_host']);

    // fixing the empty xlite_https_host value
    if (!isset($params['xlite_https_host']) || $params['xlite_https_host'] == '') {
        $params['xlite_https_host'] = $params['xlite_http_host'];
    }

    $_params = $params;

    // check whether the authcode is set in params.
    $new_config = '';

    // change config file ..
    foreach ($config as $num => $line) {

        $patterns = array();
        $replacements = array();
        foreach ($pairs as $pk => $pv) {
            $patterns[] = '/^' . $pk . '\s*=.*/';
            $replacements[] = $pk . ' = "' . (empty($_params[$pv]) ? '' : $_params[$pv]) . '"';
        }

        $patterns[] = '/^shared_secret_key.*=.*/';
        $replacements[] = 'shared_secret_key = "' . uniqid('', true) . '"';

        // check whether skin param is specified: not used at present
        if (isset($_params['skin'])) {
            $patterns[] = '/^skin.*=.*/';
            $replacements[] = 'skin = "' . $params['skin'] . '"';
        }

        $patterns[] = '/^installation_lng.*=.*/';
        $replacements[] = 'installation_lng = ' . XLITE_EDITION_LNG;

        $patterns[] = '/^default = [a-z]{2}/';
        $replacements[] = 'default = ' . XLITE_EDITION_LNG;

        $patterns[] = '/^product_clean_urls_format.*=.*/';
        $replacements[] = 'product_clean_urls_format = ' . 'domain/goalproduct';

        $patterns[] = '/^static_page_clean_urls_format.*=.*/';
        $replacements[] = 'static_page_clean_urls_format = ' . 'domain/goalpage';

        $patterns[] = '/^vendor_clean_urls_format.*=.*/';
        $replacements[] = 'vendor_clean_urls_format = ' . 'domain/goalvendor';

        $patterns[] = '/^news_clean_urls_format.*=.*/';
        $replacements[] = 'news_clean_urls_format = ' . 'domain/goalnews';

        // escape every backslash and dollar sign with a backslash
        // to avoid using them as backreferences
        $replacements = array_map(function ($replacement) {
            return strtr($replacement, array('\\\\' => '\\\\\\\\', '$' => '\\$'));
        }, $replacements);

        $new_config .= preg_replace($patterns, $replacements, $line);
    }

    return save_config($new_config);

 }

/**
 * Save content to the config.php file
 *
 * @param string $content
 *
 * @return mixed
 */
function save_config($content)
{
    $handle = fopen(LC_DIR_CONFIG . constant('LC_CONFIG_FILE'), 'wb');
    fwrite($handle, $content);
    fclose($handle);
    return $handle ? true : $handle;
}

/**
 * Returns some information from phpinfo()
 *
 * @return array
 */
function get_info()
{
    static $info;

    if (!isset($info)) {
        $info = array(
            'thread_safe'     => false,
            'debug_build'     => false,
            'php_ini_path'    => '',
            'no_mem_limit'    => true,
            'commands_exists' => false,
            'php_ini_path_forbidden' => false,
            'phar_ext_ver'    => '',
        );

    } else {
        return $info;
    }

    ob_start();
    phpinfo();
    $php_info = ob_get_contents();
    ob_end_clean();

    $dll_sfix = (LC_OS_IS_WIN ? '.dll' : '.so');

    foreach (explode("\n",$php_info) as $line) {

        if (preg_match('/command/i',$line)) {
            $info['commands_exists'] = true;

            if (preg_match('/--enable-memory-limit/i', $line)) {
                $info['no_mem_limit'] = false;
            }
            continue;
        }

        if (preg_match('/thread safety.*(enabled|yes)/i', $line)) {
            $info["thread_safe"] = true;
        }

        if (preg_match('/debug.*(enabled|yes)/i', $line)) {
            $info["debug_build"] = true;
        }

        if (preg_match("/configuration file.*(<\/B><\/td><TD ALIGN=\"left\">| => |v\">)([^ <]*)(.*<\/td.*)?/i",$line,$match)) {
            $info["php_ini_path"] = $match[2];

            // If we can't access the php.ini file then we probably lost on the match
            if (!@ini_get("safe_mode") && !@file_exists($info["php_ini_path"])) {
                $info["php_ini_path_forbidden"] = true;
            }
        }

        if (preg_match('/Phar EXT version.*<\/td><td([^>]*)>([^<]*)/i', $line, $match)) {
            $info['phar_ext_ver'] = $match[2];
        }
    }

    return $info;
}

/**
 * Do an HTTP request to the install.php
 *
 * @param string $action_str
 *
 * @return string
 */
function inst_http_request_install($action_str, $url = null)
{
    if (is_null($url)) {
        $url = getXCartURL();
    }

    $url_request = $url . '/install.php?target=install' . (($action_str) ? '&' . $action_str : '');

    return inst_http_request($url_request);
}

/**
 * Do an HTTP request to test clean URLs availability
 *
 * @return string
 */
function inst_http_request_clean_urls()
{
    $url = getXCartURL();

    $url_request = $url . '/check/for/clean/urls.html';

    $result = inst_http_request($url_request, true);

    return in_array($result, array(200, 301, 302));
}

/**
 * Returns X-Cart URL
 *
 * @return string
 */
function getXCartURL()
{
    $host = 'http://' . $_SERVER['HTTP_HOST'];
    $host = ('/' == substr($host, -1) ? substr($host, 0, -1) : $host);

    $uri = defined('LC_URI') ? constant('LC_URI') : $_SERVER['REQUEST_URI'];

    $web_dir = preg_replace('/\/install(\.php)*/', '', $uri);
    $url = $host . ('/' == substr($web_dir, -1) ? substr($web_dir, 0, -1) : $web_dir);

    return $url;
}

/**
 * Do an HTTP request
 *
 * @param string $url_request
 *
 * @return string
 */
function inst_http_request($url_request, $returnCode = false)
{
    $result = null;
    $adapter = null;
    $response = null;
    $error = null;

    try {

        $bouncer = new \PEAR2\HTTP\Request($url_request);

        $result = $bouncer->sendRequest();
        $adapter = $bouncer->getAdapterName();

        $response = $result->body;

    } catch (\Exception $exception) {
        $error = $exception->getMessage();
    }

    x_install_log(
        'inst_http_request() result',
        array(
            'url_request' => $url_request,
            'adapter'     => $adapter,
            'result'      => $result,
            'response'    => $response,
            'error'       => $error,
        )
    );

    return $returnCode && $result ? $result->code : $response;
}

/**
 * Check if memory_limit is disabled
 *
 * @return bool
 */
function is_disabled_memory_limit()
{
    $info = get_info();

    $result = (($info['no_mem_limit'] &&
                $info['commands_exists'] &&
                !function_exists('memory_get_usage') &&
                version_compare(phpversion(), '4.3.2') >= 0 &&
                strlen(@ini_get('memory_limit')) == 0 ) ||
                @ini_get('memory_limit') == '-1');

    return $result;
}

/**
 * Preparing text of the configuration checking report
 *
 * @param array $requirements
 *
 * @return string
 */
function make_check_report($requirements)
{
    global $fatalErrorMsg;

    $phpinfo_disabled = false;

    $report = array();
    $report[] = 'X-Cart version ' . constant('LC_VERSION');
    $report[] = 'Report time stamp: ' . date('d, M Y  H:i');
    $report[] = '';

    if (!empty($fatalErrorMsg)) {
        $report[] = 'FATAL ERROR: ' . $fatalErrorMsg;
        $report[] = '';
    }

    $errors = array();
    $all = array();

    foreach ($requirements as $reqName => $reqData) {

        if ($reqName === 'mysql_version' && $reqData['data']['version'] === 'unknown') {
            continue;
        }

        $rep = array();
        $rep[] = '[' . $reqData['title'] . ']';
        $rep[] = 'Check result  - ' . (isset($reqData['skipped']) ? 'SKIPPED' : (($reqData['status']) ? 'OK' : 'FAILED'));
        $rep[] = 'Critical  - ' . (($reqData['critical']) ? 'Yes' : 'No');

        if (!empty($reqData['value'])) {
            $rep[] = $reqData['title'] . ' - ' . $reqData['value'];
        }

        if (!$reqData['status'] || isset($reqData['skipped'])) {
            $rep[] = $reqData['description'];
        }

        $rep[] = '';

        if (isset($reqData['skipped']) || $reqData['status']) {
            $all = array_merge($all, $rep);

        } else {
            $errors = array_merge($errors, $rep);
        }
    }

    if ($errors) {
        $report[] = 'ERRORS:';
        $report[] = '';
        foreach ($errors as $row) {
            $report[] = $row;
        }
        $report[] = '';
    }

    if ($all) {
        $report[] = 'The rest report data:';
        $report[] = '';
        foreach ($all as $row) {
            $report[] = $row;
        }
        $report[] = '';
    }

    $report[] = '';
    $report[] = '============================= PHP info =============================';
    $report[] = '';

    $report = strip_tags(implode("\n", $report));

    if (function_exists('phpinfo')) {
        // display PHP info
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        // prepare phpinfo
        $phpinfo = preg_replace("/<td[^>]+>/i", " | ", $phpinfo);
        $phpinfo = preg_replace("/<[^>]+>/i", "", $phpinfo);
        $phpinfo = preg_replace("/(?:&lt;)((?!&gt;).)*?&gt;/i", "", $phpinfo);

        $pos = strpos($phpinfo, 'PHP Version');
        if ($pos !== false) {
            $phpinfo = substr_replace($phpinfo, "", 0, $pos);
        }

        $pos = strpos($phpinfo, 'PHP License');
        if ($pos !== false) {
            $phpinfo = substr($phpinfo, 0, $pos);
        }
    } else {
        $phpinfo .= "phpinfo() disabled.\n";
    }

    $report .= $phpinfo;

    return $report;
}

/**
 * Display GA event
 *
 * @param string  $category Event category (step, button, error, warning)
 * @param string  $action   Category action (step-N, click, file, pdo, params, reqs, auth code, re-install)
 * @param string  $label    Event label (pdo error text, req name etc) OPTIONAL
 * @param integer $value    Event value (not used at the moment) OPTIONAL
 *
 * @return void
 */
function ga_event($category, $action, $label = null, $value = null)
{
    if ('pdo' == $action) {
        $label = preg_replace('/^.*SQLSTATE ?\[(\w+)\] ?\[(\w+)\].*$/Ss', '\\1-\\2', $label);
    }

    $params = array();
    $params[] = $category;
    $params[] = $action;

    if ($label) {
        $params[] = $label;
    }

    foreach ($params as $k => $v) {
        $params[$k] = "'" . str_replace("'", "\\'", $v) . "'";
    }

    if (0 < intval($value)) {
        $params[] = intval($value);
    }

    $paramsStr = implode(',', $params);

    print <<<OUT
<script type="text/javascript">
  ga('send','event',{$paramsStr});
</script>
OUT;
}

/**
 * Return status message
 *
 * @param bool   $status Status to display: true or false
 * @param string $code   Code of section with status details (<div id='$code'>)
 *
 * @return bool
 */
function status($status, $code = null)
{
    global $first_error;

    if ($code != null) {

        if ($first_error == null && !$status) {
            $first_error = $code;
        }

        if ($status) {
            $return = '<span class="status-ok">OK</span>';

        } else {
            $return = '<a class="status-failed-link" id="failed-' . $code . '" href="javascript: showDetails(\'' . $code  . '\', ' . (isHardError($code) ? 'true' : 'false') . ');" onclick=\'this.blur();\' title=\'' . xtr('Click here to see more details') . '\'>' . xtr('Failed') . '</a>';
        }

    } else {
        $return = $status
            ? '<span class="status-ok">OK</span>'
            : '<span class="status-failed">' . xtr('Failed') . '</span>';
    }

    return $return;
}

/**
 * Return status 'skipped' message
 *
 * @return string
 */
function status_skipped()
{
    return '<span class="status-skipped">' . xtr('Skipped') . '</span>';
}

/**
 * Display fatal_error message
 *
 * @param string $txt
 * @param string $errorCategory
 * @param string $errorCode
 *
 * @return void
 */
function fatal_error($txt, $errorCategory, $errorCode) {

    x_install_log(xtr('Fatal error') . ': ' . $txt);

    ga_event('error', $errorCategory, $errorCode);
?>

<div class="fatal-error"><?php echo xtr('Fatal error'); ?>: <?php echo $txt ?><br /><?php echo xtr('Please correct the error(s) before proceeding to the next step.'); ?></div>
<?php
}

/**
 * Display extended fatal_error message
 *
 * @param string $txt
 * @param string $errorCategory
 * @param string $errorCode
 * @param string $additionalNote
 *
 * @return void
 */
function fatal_error_extended($txt, $errorCategory, $errorCode, $additionalNote = '')
{
    global $fatalErrorMsg;
    global $displayHelpButton;

    $fatalErrorMsg = $txt;

    x_install_log(xtr('Fatal error') . ': ' . $txt);

    ga_event('error', $errorCategory, $errorCode);
?>

<div class="fatal-error extended">
    <div class="header"><?php echo xtr('Fatal error'); ?></div>
    <div><?php echo $txt ?></div>
    <div class="additional-note">
        <?php echo !empty($additionalNote) ? $additionalNote : ''; ?>
        <?php echo xtr('Please correct the error(s) before proceeding to the next step or get help.'); ?>
    </div>
<?php
    x_display_help_block(false);
?>

</div>

<?php
    $displayHelpButton = true;
}

/**
 * Display block 'Create Online Store with X‑Cart...'
 *
 * @param boolean $hidden Flag: is block should be hidden (true) or visible (false)
 *
 * @return void
 */
function x_display_help_block($hidden = true)
{
    global $params;

    $url = 'https://www.x-cart.com/create-online-store.html?'
        . (!empty($params['login']) ? 'email=' . urlencode($params['login']) . '&amp;' : '')
        . 'utm_source=XC5Install&amp;utm_medium=reqsFailure&amp;utm_campaign=XC5Install';

?>
<div id="suppose-cloud" class="cloud-box" <?php echo $hidden ? 'style="display: none;"' : ''; ?>>

    <div class="grey-line">
        <div class="or-cloud"><span>OR</span></div>
    </div>

    <?php x_display_hosting_icon(); ?>

    <div class="cloud-header"><?php echo xtr('Create Online Store with X‑Cart!'); ?></div>
    <div class="cloud-text"><?php echo xtr('No limitations or transaction fees, Open source code for easy customization, Great 24/7 support'); ?></div>

<?php if ($hidden) { ?>

    <a href="<?php echo $url; ?>" target="_blank"><?php echo xtr('Get first 30 days for FREE'); ?></a>

<?php } else { ?>

  <input type="button" id="create-online-store" class="btn btn-default btn-lg" value="<?php echo xtr('Get first 30 days for FREE'); ?>" onclick="javascript:window.open('<?php echo $url; ?>');" />

<?php } ?>

</div>

<?php
}

/**
 * Display the hosting svg-icon
 *
 * @return void
 */
function x_display_hosting_icon()
{
?>

<div class="svg-icon hosting">
    <img src="skins/admin/images/icon-hosting.svg" />
</div>

<?php
}

/**
 * Display warning_error message
 *
 * @param string $txt
 * @param string $errorCategory
 * @param string $errorCode OPTIONAL
 *
 * @return void
 */
function warning_error($txt, $errorCategory, $errorCode = '') {

    x_install_log(xtr('Warning') . ': ' . $txt);

    ga_event('warning', $errorCategory, $errorCode);
?>

<div class="warning-text">
    <?php echo xtr('Warning'); ?>: <?php echo $txt ?>
</div>
<?php

}

/**
 * Display message
 *
 * @param string $txt
 *
 * @return void
 */
function message($txt) {
?>
<b><span class=WelcomeTitle><?php echo $txt ?></span></b>
<?php
}

/**
 * Replace install.php script to random filename
 *
 * @return mixed
 */
function rename_install_script()
{
    $install_name = 'install.'.md5(uniqid(mt_rand(), true)) . '.php';
    @rename(LC_DIR_ROOT . 'install.php', LC_DIR_ROOT . $install_name);
    @clearstatcache();

    $result = (!file_exists(LC_DIR_ROOT . 'install.php') && file_exists(LC_DIR_ROOT . $install_name) ? $install_name : false);

    if ($result) {
        x_install_log(xtr('Installation script renamed to :filename', array(':filename' => $install_name)));

    } else {
        x_install_log(xtr('Warning! Installation script renaming failed'));
    }

    return $result;
}

/**
 * Get number for StepBack button
 *
 * @return integer
 */
function getStepBackNumber()
{
    global $current, $params;

    switch ($current) {
        case 4:
            $back = 2;
            break;

        case 5:
            $back = 3;
            break;

        default:
            $back = ($current > 0 ? $current - 1 : 0);
            break;
    }

    if (isset($params['start_at'])
        && (($params['start_at'] === '4' && $back < 4) || ($params['start_at'] === '6' && $back < 6))
    ) {
        $back = 0;
    }

    return $back;
}

/**
 * Default navigation button handler: default_js_back
 *
 * @return string
 */
function default_js_back()
{
?>
    function step_back() {
        document.ifrm.current.value = "<?php echo getStepBackNumber(); ?>";
        document.ifrm.submit();
        return true;
    }
<?php
}

/**
 * Default navigation button handler: default_js_next
 *
 * @return string
 */
function default_js_next()
{
?>
    function step_next() {
        return true;
    }
<?php
}

/**
 * Generate Auth code
 *
 * @return string
 */
function generate_authcode()
{
    // see include/functions.php
    return generate_code(32);
}

/**
 * Check Auth code (exit if wrong)
 *
 * @param array $params
 *
 * @return void
 */
function check_authcode(&$params)
{
    global $error;

    $authcode = get_authcode();

    // if authcode IS NULL, then this is probably the first install, skip
    // authcode check
    if (is_null($authcode)) {
        return;
    }

    if (!isset($params['auth_code']) || trim($params['auth_code']) != $authcode) {
        fatal_error(xtr('Incorrect auth code! You cannot proceed with the installation.'), 'auth code', 'incorrect auth code');
        $error = true;
    }
}

/**
 * Read config file and get Auth code
 *
 * @return mixed
 */
function get_authcode()
{
    global $error;

    if (!$data = parse_config()) {
        fatal_error(xtr('Config file not found (:filename)', array(':filename' => constant('LC_CONFIG_FILE'))), 'file', 'config file not found');
        $error = true;
    }

    return !empty($data['auth_code']) ? $data['auth_code'] : null;
}

/**
 * Read config files and returns array of options
 *
 * @return array
 */
function parse_config()
{
    $result = array();

    $configFiles = array(
        constant('LC_DEFAULT_CONFIG_FILE'),
        constant('LC_CONFIG_FILE'),
    );

    foreach ($configFiles as $configFile) {

        if (file_exists(LC_DIR_CONFIG . $configFile)) {

            $data = @parse_ini_file(LC_DIR_CONFIG . $configFile);

            if (!empty($data) && is_array($data)) {
                $result = array_replace_recursive($result, $data);
            }
        }
    }

    return $result;
}

/**
 * Update configuration settings in the database
 *
 * @param array $params Database access data and other parameters
 *
 * @return void
 */
function update_config_settings($params)
{
    $siteEmail = (!empty($params['site_mail']) ? $params['site_mail'] : $params['login']);
    $defaultTimezone = (!empty($params['date_default_timezone']) ? $params['date_default_timezone'] : @date_default_timezone_get());

    $time = \XLite\Core\Converter::time();

    $serializedSiteEmail = serialize([$siteEmail]);
    $options = array(
        'Company::orders_department'  => $serializedSiteEmail,
        'Company::site_administrator' => $serializedSiteEmail,
        'Company::support_department' => $serializedSiteEmail,
        'Company::users_department'   => $serializedSiteEmail,
        'Units::time_zone'            => $defaultTimezone,
        'Company::start_year'         => date('Y', $time),
        'Version::timestamp'          => $time,
        'CleanURL::clean_url_flag'    => inst_http_request_clean_urls(),
    );

    if (!$options['CleanURL::clean_url_flag']) {
        $options['General::terms_url'] = 'cart.php?target=page&id=1';
    }

    foreach ($options as $key => $value) {

        list($cat, $name) = explode('::', $key);

        $configOption = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(array('category' => $cat, 'name' => $name));

        if (isset($configOption)) {
            $configOption->setValue($value);
            \XLite\Core\Database::getRepo('XLite\Model\Config')->update($configOption);

        } else {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => $cat,
                    'name'     => $name,
                    'value'    => $value,
                )
            );
        }
    }

    \XLite\Core\Database::getEM()->flush();
}

/**
 * Update system data storage
 *
 * @param array $params data
 *
 * @return void
 */
function updateSystemDataStorage($params)
{
    $systemData = \XLite\Core\Marketplace::getInstance()->getSystemData();

    $systemData['adminEmail'] = $params['site_mail'] ?? $params['login'];
    $systemData['freshInstall'] = true;
    $systemData['shopCountryCode'] = \XLite\Core\Config::getInstance()->Company->location_country;

    \XLite\Core\Marketplace::getInstance()->setSystemData($systemData);
}

/**
 * Save Auth code
 *
 * @param array $params
 *
 * @return string
 */
function save_authcode(&$params) {

    // if authcode set in request, don't change the config file
    if (isset($params['auth_code']) && trim($params['auth_code']) != '') {
        return $params['auth_code'];
    }

    // generate new authcode
    $auth_code = generate_authcode();

    if (!@is_writable(LC_DIR_CONFIG . constant('LC_CONFIG_FILE')) || !$config = file(LC_DIR_CONFIG . constant('LC_CONFIG_FILE'))) {
        message(xtr('Cannot open config file \':filename\' for writing!', array(':filename' => constant('LC_CONFIG_FILE'))));
        exit();
    }

    $new_config = '';

    foreach ($config as $num => $line) {
        $new_config .= preg_replace('/^auth_code.*=.*/', 'auth_code = "'.$auth_code.'"', $line);
    }

    if (!save_config($new_config)) {
        message(xtr('Config file \':filename\' write failed!', array(':filename' => constant('LC_CONFIG_FILE'))));
        exit();
    }

    return get_authcode();
}

/**
 * Get step by module name
 *
 * @param string $name
 *
 * @return int
 */
function get_step($name)
{
    global $modules;

    $result = 0;

    foreach ($modules as $step => $module_data) {

        if ($module_data['name'] == $name) {
            $result = $step;
            break;
        }
    }

    return $result;
}

/**
 * Get timezones list
 *
 * @param boolean $addBlank Flag: is the blank row must prepend the rest of list rows
 *
 * @return array
 */
function getTimeZones($addBlank = false)
{
    $result = $addBlank
        ? array(
            '' => xtr('- None selected -')
        )
        : array();

    $zones = timezone_identifiers_list();

    foreach ($zones as $zone) {

        if (preg_match('!^((Africa|America|Antarctica|Arctic|Asia|Atlantic|Australia|Europe|Indian|Pacific)/|UTC$)!', $zone)) {

            $date = date_create(null, new DateTimeZone($zone));

            $result[$zone] = xtr(
                '@zone - [GMT@diff] - (@date)',
                array(
                    '@zone' => xtr(str_replace('_', ' ', $zone)),
                    '@diff' => date_format($date, 'P'),
                    '@date' => date_format($date, 'M j, Y - H:i'),
                )
            );
        }
    }

    ksort($result);

    return $result;
}

/**
 * Display form element
 *
 * @param string $fieldName
 * @param array  $fieldData
 *
 * @return string
 */
function displayFormElement($fieldName, $fieldData)
{
    global $currentSection;

    if (!isset($currentSection)) {
        $currentSection = null;
    }

    $fieldType = (isset($fieldData['type']) ? $fieldData['type'] : 'text');
    $fieldValue = (isset($fieldData['value']) ? $fieldData['value'] : $fieldData['def_value']);

    switch ($fieldType) {

        // Drop down box
        case 'select': {

            $formElement =<<<OUT
        <select class="form-control" id="param-{$fieldName}" name="params[{$fieldName}]">
OUT;

            if (is_array($fieldData['select_data'])) {
                foreach ($fieldData['select_data'] as $key => $value) {
                    $_selected = ($key == $fieldValue ? ' selected="selected"' : '');
                    $formElement .=<<<OUT
            <option value="{$key}"{$_selected}>{$value}</option>
OUT;
                }
            }

            $formElement .=<<<OUT
        </select>
OUT;

            break;
        }

        // Checkbox
        case 'checkbox': {

            $_checked = !empty($fieldValue) ? 'checked="checked" ' : '';
            $formElement =<<<OUT
        <input type="checkbox" name="params[{$fieldName}]" id="param-{$fieldName}" value="Y" {$_checked} />
OUT;
            break;
        }

        // Static text (not for input)
        case 'static': {
            $formElement = $fieldValue;
            break;
        }

        // Input text
        case 'text':
        case 'password' :
        default: {

            $fieldType = (in_array($fieldType, array('text', 'password')) ? $fieldType : 'text');
            $disableAutofill = isset($fieldData['disableAutofill']) && $fieldData['disableAutofill'] ? 'autocomplete="off" readonly' : '';
            $formElement =<<<OUT
        <input class="form-control" type="{$fieldType}" id="param-{$fieldName}" name="params[{$fieldName}]" class="full-width" value="{$fieldValue}" {$disableAutofill} />
OUT;
        }
    }

    $requiredField = $fieldData['required'] ? '<span class="required">*</span>' : '';

    $section = !empty($fieldData['section']) ? $fieldData['section'] : '';
    $sectionCSS = $section ? 'section section-' . $section : '';

    $sections = array(
        'advanced-server' => xtr('Advanced server settings'),
        'advanced-mysql'  => xtr('Advanced MySQL settings'),
    );

    $output = '';

    if ($currentSection != $section) {
        $currentSection = $section;
        if (!empty($currentSection)) {
            $output .=<<<OUT
    <tr class="section-title section-{$currentSection}">
        <td><span onclick="javascript: toggleSection('{$currentSection}');">{$sections[$currentSection]}</span></td>
    </tr>
OUT;
        }
    }

    $notice = $fieldData["description"]
        ? "<div class='field-notice'>{$fieldData['description']}</div>"
        : "";

    $output .=<<<OUT
    <tr class="{$sectionCSS}">
        <td class="table-left-column"><div class="field-label">{$fieldData['title']}{$requiredField}</div></td>
        <td class="table-right-column">{$formElement}{$notice}</td>
    </tr>
OUT;

    echo $output;
}

/*
 * End of Service functions section
 */

/*
 * Modules section
 */

/**
 * Default module. Shows Terms & Conditions
 *
 * @param array $params
 *
 * @return bool
 */
function module_default(&$params)
{
    global $error;

    include LC_DIR_ROOT . 'Includes/install/templates/step0_copyright.tpl.php';

    return false;
}

/**
 * 'Next' button handler. Checking if an 'Agree' checkbox was ticked
 *
 * @return string
 */
function module_default_js_next()
{
?>
    function step_next() {

        var result = true;
        var isAgree = document.ifrm.agree.checked;

        if (!isAgree) {
            alert("<?php echo xtr('You must accept the License Agreement to proceed with the installation. If you do not agree with the terms of the License Agreement, do not install the software.'); ?>");
            result = false;
        }

        return result;
    }
<?php
}


/**
 * Configuration checking module
 *
 * @return bool
 */
function module_check_cfg(&$params)
{
    global $first_error, $error, $report_uid, $reportFName, $tryAgain, $skinsDir;
    global $requirements;
    global $params;


    // Check login and password values

    $login = $params['login'];
    $password = $params["password"];

    if (empty($login) || empty($password)) {
        $result = false;
        $errorMsg = fatal_error(xtr('Login and password can\'t be empty.'), 'params', 'empty admin login or password');

    } else {
        $password = md5($password);
    }

    $params['_login'] = $params['login'];
    $params['_password'] = $params['password'];

    // Check requirements
    $requirements = doCheckRequirements();

    $errorsFound = false;
    $warningsFound = false;

    $sections = [
        'A' => xtr('Environment checking'),
        'B' => xtr('Inspecting server configuration'),
    ];

    $steps = [
        [
            'title'        => xtr('Critical dependencies'),
            'error_msg'    => xtr('Critical dependency failed'),
            'section'      => 'B',
            'requirements' => [
                'php_version',
                'php_memory_limit',
                'doc_blocks_support',
                'php_pdo_mysql',
                'config_file',
                'file_permissions',
                'frame_options'
            ]
        ],
        [
            'title'        => xtr('Non-critical dependencies'),
            'error_msg'    => xtr('Non-critical dependency failed'),
            'section'      => 'B',
            'requirements' => [
                'php_disabled_functions',
                'php_file_uploads',
                'php_upload_max_file_size',
                'php_gdlib',
                'php_phar',
                'https_bouncer',
                'xml_support',
                'loopback_request'
            ]
        ]
    ];

    ob_start();
    require_once LC_DIR_ROOT . 'Includes/install/templates/step1_chkconfig.tpl.php';
    $content = ob_get_contents();
    ob_clean();

    $error = $errorsFound;
    $tryAgain = $errorsFound || $warningsFound;

    if (!$tryAgain) {
        $params['requirements_success'] = true;
        global $autoPost;
        $autoPost = true;

    } else {
        echo $content;
    }

    return false;
}

/**
 * Add default values for parameters
 *
 * @param array &$paramFields List of parameters
 *
 * @return void
 */
function applySuggestedDefValues(&$paramFields)
{
    $paramFields['mysqlhost']['def_value'] = ini_get('mysqli.default_host') ?: (ini_get('mysql.default_host') ?: 'localhost');
    $paramFields['mysqlbase']['def_value'] = 'xc5';
    $paramFields['mysqluser']['def_value'] = ini_get('mysqli.default_user') ?: (ini_get('mysql.default_user') ?: '');
    $paramFields['mysqlpass']['def_value'] = ini_get('mysqli.default_pw') ?: (ini_get('mysql.default_password') ?: '');
    $paramFields['mysqlport']['def_value'] = ini_get('mysqli.default_port') ?: (ini_get('mysql.default_port') ?: '');
    $paramFields['mysqlsock']['def_value'] = ini_get('pdo_mysql.default_socket') ?: (ini_get('mysqli.default_socket') ?: (ini_get('mysql.default_socket') ?: ''));
    $paramFields['mysqlprefix']['def_value'] = 'xc_';
    $paramFields['mysqlcharset']['def_value'] = 'utf8mb4';

    $paramFields['demo']['def_value'] = '1';

    $paramFields['xlite_http_host']['def_value'] = $_SERVER['HTTP_HOST'];
    $paramFields['xlite_https_host']['def_value'] = $_SERVER['HTTP_HOST'];
    $paramFields['xlite_web_dir']['def_value'] = preg_replace('/\/install(\.php)*/Ss', '', $_SERVER['REQUEST_URI']);
    $paramFields['date_default_timezone']['def_value'] = date_default_timezone_get();
}

/**
 * Do step of gathering of the database configuration
 *
 * @param array $params
 *
 * @return bool
 */
function module_cfg_install_db(&$params)
{
    global $error, $lcSettings;
    global $report_uid, $reportFName;
    global $checkRequirements;
    global $requirements;
    global $isDBConnected;

    $pdoErrorMsg = '';
    $output = '';

    $requirements = doCheckRequirements();

    // Remove report file if it was created on the previous step
    if (@file_exists($reportFName)) {
        @unlink($reportFName);
        $report_uid = '';
    }

    $paramFields = array(
        'mysqlhost'        => array(
            'title'       => xtr('MySQL server name'),
            'description' => xtr('Hostname or IP address of your MySQL server.'),
            'required'    => true,
        ),
        'mysqlbase'        => array(
            'title'       => xtr('MySQL database name'),
            'description' => xtr('Name of a new or existing database to use.'),
            'required'    => true,
        ),
        'mysqluser'        => array(
            'title'       => xtr('MySQL username'),
            'description' => xtr('MySQL username. The user must have full access to the database specified above.'),
            'required'    => true,
            'disableAutofill' => true,
        ),
        'mysqlpass'        => array(
            'title'       => xtr('MySQL password'),
            'description' => xtr('Password for the above MySQL username.'),
            'required'    => false,
            'type'        => 'password',
            'disableAutofill' => true,
        ),
        'demo'             => array(
            'title'       => xtr('Install sample catalog'),
            'description' => xtr('Specify whether you would like to setup sample categories and products?'),
            'required'    => false,
            'type'        => 'checkbox',
        ),
        'mysqlport'        => array(
            'title'       => xtr('MySQL server port'),
            'description' => xtr('If your database server is listening to a non-standard port, specify its number (e.g. 3306).'),
            'required'    => false,
            'section'     => 'advanced-mysql',
        ),
        'mysqlsock'        => array(
            'title'       => xtr('MySQL server socket'),
            'description' => xtr('If your database server is used a non-standard socket, specify it (e.g. /tmp/mysql-5.1.34.sock).'),
            'required'    => false,
            'section'     => 'advanced-mysql',
        ),
        'mysqlprefix'        => array(
            'title'       => xtr('MySQL tables prefix'),
            'description' => xtr('The prefix of the shop tables in database'),
            'required'    => true,
            'section'     => 'advanced-mysql',
        ),
        'mysqlcharset'       => array(
            'title'       => xtr('Default charset'),
            'description' => '',
            'select_data' => ['utf8mb4' => 'utf8mb4', 'utf8' => 'utf8'],
            'required'    => false,
            'type'        => 'select',
            'section'     => 'advanced-mysql',
        ),
        'xlite_http_host'  => array(
            'title'       => xtr('Web server name'),
            'description' => xtr('Hostname of your web server (E.g.: www.example.com).'),
            'required'    => true,
            'section'     => 'advanced-server',
        ),
        'xlite_https_host' => array(
            'title'       => xtr('Secure web server name'),
            'description' => xtr('Hostname of your secure (HTTPS-enabled) web server (E.g.: secure.example.com). If omitted, it is assumed to be the same as the web server name.'),
            'required'    => false,
            'section'     => 'advanced-server',
        ),
        'xlite_web_dir'    => array(
            'title'       => xtr('X-Cart web directory'),
            'description' => xtr('Path to X-Cart files within the web space of your web server (E.g.: /shop).'),
            'required'    => false,
            'section'     => 'advanced-server',
        ),
        'date_default_timezone' => array(
            'title'       => xtr('Default time zone'),
            'description' => xtr('By default, dates in this site will be displayed in the chosen time zone.'),
            'select_data' => getTimeZones(true),
            'required'    => false,
            'type'        => 'select',
            'section'     => 'advanced-server',
        )
    );

    // Initialize default values for parameters
    applySuggestedDefValues($paramFields);

    if ($requirements['loopback_request']['status'] === false) {
        unset($paramFields['demo']);
        $params['demo'] = 'Y';
    }

    $messageText = '';

    $displayConfigForm = false;

    foreach ($paramFields as $fieldName => $fieldData) {

        // Prepare first step data if we came from the second step back
        if (isset($_POST['go_back']) && $_POST['go_back'] === '1') {
            if (empty($fieldData['step']) && isset($params[$fieldName])) {
                $paramFields[$fieldName]['def_value'] = $params[$fieldName];
                unset($params[$fieldName]);
            }
        }

        // Unset parameter if its empty
        if (isset($params[$fieldName])) {
            $params[$fieldName] = trim($params[$fieldName]);

            if (empty($params[$fieldName])) {
                unset($params[$fieldName]);
            }
        }

        // Check if all required parameters presented
        if (!isset($params[$fieldName])) {
            $displayConfigForm = $displayConfigForm || $fieldData['required'];
        }
    }

    // Display form to enter host data and database settings
    if ($displayConfigForm) {

        ob_start();

        foreach ($paramFields as $fieldName => $fieldData) {

            if (isset($fieldData['step']) && $fieldData['step'] != 1) {
                continue;
            }

            $fieldData['value'] = (isset($params[$fieldName]) ? $params[$fieldName] : $fieldData['def_value']);

            displayFormElement($fieldName, $fieldData);
        }

        $output = ob_get_contents();
        ob_end_clean();

?>

<input type="hidden" name="cfg_install_db_step" value="1" />

<?php


    // Display second step: review parameters and enter additional data
    } else {

        // Now checking if database named $params[mysqlbase] already exists

        $checkError = false;
        $checkWarning = false;

        if (strstr($params['xlite_http_host'], ':')) {
            list($_host, $_port) = explode(':', $params['xlite_http_host']);

        } else {
            $_host = $params['xlite_http_host'];
        }

        if (!$_host) {
            fatal_error(xtr('The web server name and/or web drectory is invalid (:host). Press \'BACK\' button and review web server settings you provided', array(':host' => $_host)), 'params', 'wrong web server or webdir');
            $checkError = true;

        // Check if database settings provided are valid
        } else {

            $connection = dbConnect($params, $pdoErrorMsg);

            if (!$connection && preg_match('/SQLSTATE.*\[1049\].*' . preg_quote($params['mysqlbase']) . '/i', $pdoErrorMsg)) {

                // The specified database not found, try to create

                x_install_log('The specified database "' . $params['mysqlbase'] . '" not found');

                $paramsNoDb = $params;
                unset($paramsNoDb['mysqlbase']);

                $pdoErrorMsg = null;
                $connection = dbConnect($paramsNoDb, $pdoErrorMsg);

                if ($connection) {

                    $pdoErrorMsg = null;
                    dbExecute('CREATE DATABASE `' . $params['mysqlbase'] . '`;', $pdoErrorMsg);

                    if (empty($pdoErrorMsg)) {

                        x_install_log('The database "' . $params['mysqlbase'] . '" successfully created.');

                        // Reconnect...
                        $connection = dbConnect($params, $pdoErrorMsg);

                    } else {
                        $connection = null;
                        x_install_log('The database "' . $params['mysqlbase'] . '" cannot be created: ' . $pdoErrorMsg);
                        fatal_error_extended(
                            xtr(
                                'The database <i>:dbname</i> cannot be created automatically:pdoerr.<br /> Please go back, create it manually and then proceed with the installation process again.',
                                array(
                                    ':dbname' => $params['mysqlbase'],
                                    ':pdoerr' => ': ' . $pdoErrorMsg
                                )
                            ),
                            'pdo',
                            @$pdoErrorMsg,
                            xtr('kb_note_mysql_issue')
                        );
                        $checkError = true;
                    }
                }
            }

            if ($connection) {

                $isDBConnected = true;

                $fields = array(
                    'hostspec' => 'mysqlhost',
                    'port'     => 'mysqlport',
                    'socket'   => 'mysqlsock',
                    'username' => 'mysqluser',
                    'password' => 'mysqlpass',
                    'database' => 'mysqlbase',
                );
                $dbParams = array();
                foreach ($fields as $key => $value) {
                    if (isset($params[$value])) {
                        $dbParams[$key] = $params[$value];
                    }
                }

                $requirements = doCheckRequirements([
                    'databaseDetails' => $dbParams
                ]);

                // Check MySQL version
                $mysqlVersionErr = $currentMysqlVersion = '';

                if ($requirements['mysql_version'] && !$requirements['mysql_version']['status']) {
                    $currentMysqlVersion = $requirements['mysql_version']['data']['version'];
                    $mysqlVersionErr = $requirements['mysql_version']['description'];
                    fatal_error_extended($mysqlVersionErr . (!empty($currentMysqlVersion) ? '<br />(current version is ' . $currentMysqlVersion . ')' : ''), 'reqs', 'mysql version', xtr('kb_note_mysql_issue'));
                    $checkError = true;
                }

                // Check if config.php file is writeable
                if (!$checkError && !@is_writable(LC_DIR_CONFIG . constant('LC_CONFIG_FILE'))) {
                    fatal_error(xtr('Cannot open file \':filename\' for writing. To install the software, please correct the problem and start the installation again...', array(':filename' => constant('LC_CONFIG_FILE'))), 'file', 'config write failed');
                    $checkError = true;

                } elseif (!$checkError) {
                    // Check if X-Cart tables is already exists
                    $prefix = $params['mysqlprefix'] ?? get_db_tables_prefix();
                    $res = dbFetchAll('SHOW TABLES LIKE \'' . $prefix . '%\'');

                    if (is_array($res) && !empty($res)) {
                        warning_error(xtr('Installation Wizard has detected X-Cart tables'), 're-install');
                        $checkWarning = true;
                    }
                }

            } elseif (!$checkError) {

                preg_match('/SQLSTATE.*\[(\d+)\].*/', $pdoErrorMsg, $match);
                $code = intval(!empty($match[1]) ? $match[1] : 0);

                if ('1045' == $code) {
                    fatal_error_extended(xtr('pdo-error-1045', array(':pdoerr' => (!empty($pdoErrorMsg) ? $pdoErrorMsg : ''))), 'pdo', @$pdoErrorMsg, xtr('kb_note_mysql_issue'));

                } elseif ('1044' == $code) {
                    fatal_error_extended(xtr('pdo-error-1044', array(':dbuser' => $params['mysqluser'], ':dbname' => $params['mysqlbase'], ':pdoerr' => (!empty($pdoErrorMsg) ? $pdoErrorMsg : ''))), 'pdo', @$pdoErrorMsg, xtr('kb_note_mysql_issue'));

                } elseif ('2005' == $code) {
                    fatal_error_extended(xtr('pdo-error-2005', array(':pdoerr' => (!empty($pdoErrorMsg) ? $pdoErrorMsg : ''))), 'pdo', @$pdoErrorMsg, xtr('kb_note_mysql_issue'));

                } else {
                    fatal_error_extended(xtr('pdo-error-common', array(':pdoerr' => (!empty($pdoErrorMsg) ? $pdoErrorMsg : ''))), 'pdo', @$pdoErrorMsg, xtr('kb_note_mysql_issue'));
                }

                $checkError = true;
            }
        }

        if (!$checkError && !$checkWarning) {

            global $autoPost;

            $autoPost = true;

        } else {
            $output = '';
        }

        $error = $checkError;
    }

?>

<?php echo $messageText; ?>

<table width="100%" border="0" cellpadding="10">

<?php echo $output; ?>

</table>

<?php

    return $displayConfigForm;
}


/**
 * Output Javascript handler: module_cfg_install_db_js_back
 *
 * @return string
 */
function module_cfg_install_db_js_back()
{
    global $params;

    $back1 = !empty($params['requirements_success']) ? '1' : '2';

    // 1 - step back; 2 - step 2/initial form
    $goBack = (!isset($_POST['cfg_install_db_step']) ? $back1 : '3');

?>
    function step_back() {
        document.ifrm.current.value = "<?php echo $goBack; ?>";
        document.ifrm.submit();

        return true;
    }
<?php
}

/**
 * Output Javascript handler: module_cfg_install_db_js_next
 *
 * @return string
 */
function module_cfg_install_db_js_next()
{
?>
    function step_next() {
        var el = '';
        var msg = '';

        for (var i = 0; i < document.ifrm.elements.length; i++) {

            if (document.ifrm.elements[i].name.search("xlite_http_host") != -1) {
                if (document.ifrm.elements[i].value == "") {
                    el = document.ifrm.elements[i];
                    msg = "<?php echo xtr('You must provide web server name'); ?>";
                }
            }

            if (document.ifrm.elements[i].name.search("mysqlhost") != -1) {
                if (document.ifrm.elements[i].value == "") {
                    el = document.ifrm.elements[i];
                    msg = "<?php echo xtr('You must provide MySQL server name'); ?>";
                }
            }

            if (document.ifrm.elements[i].name.search("mysqluser") != -1) {
                if (document.ifrm.elements[i].value == "") {
                    el = document.ifrm.elements[i];
                    msg = "<?php echo xtr('You must provide MySQL username'); ?>";
                }
            }

            if (document.ifrm.elements[i].name.search("mysqlbase") != -1) {
                if (document.ifrm.elements[i].value == "") {
                    el = document.ifrm.elements[i];
                    msg = "<?php echo xtr('You must provide MySQL database name'); ?>";
                }
            }

            if (document.ifrm.elements[i].name.search("mysqlprefix") != -1) {
                if (document.ifrm.elements[i].value == "") {
                    el = document.ifrm.elements[i];
                    msg = "<?php echo xtr('You must provide prefix for table names in the database'); ?>";
                }
            }

            if (el) {
                el.className += ' error';
                alert(msg);
                el.focus();
                return false;
            }
        }
        return true;
    }
<?php
}

/**
 * Building cache and installing database
 *
 * @param array   $params
 * @param boolean $silentMode Silent mode
 *
 * @return bool
 */
function module_install_cache(&$params, $silentMode = false)
{
    global $error, $lcSettings;

    $result = false;

    if (!empty($params['new_installation']) && isset($params['demo']) && 'Y' == $params['demo']) {

        // Get all dump_*.sql files
        $dumpFiles = array();

        $errorFiles = false;

        foreach ((array) glob(LC_DIR_ROOT . 'dump_*.sql') as $dFile) {

            if (is_readable($dFile)) {
                $dumpFiles[] = $dFile;

            } else {
                $errorFiles = true;
                break;
            }
        }

        if (!$errorFiles && $dumpFiles) {

            echo xtr('Uploading dump.sql into database...');

            $randPrefix = mt_rand(0, 99);

            // Drop existing X-Cart tables
            if (doDropDatabaseTables($params)) {

                $result = true;

                foreach ($dumpFiles as $dump_file) {

                    $sql = file_get_contents($dump_file);
                    $sql = str_replace('`xlite_', '`' . $params['mysqlprefix'], $sql);
                    $sql = str_replace('`FK_',  '`FK_'  . $randPrefix, $sql);
                    $sql = str_replace('`IDX_', '`IDX_' . $randPrefix, $sql);

                    if ($params['mysqlcharset'] !== 'utf8') {
                        $sql = str_replace('utf8', $params['mysqlcharset'], $sql);
                    }

                    // Load SQL dump to the database
                    $pdoErrorMsg = '';
                    dbExecute($sql, $pdoErrorMsg);
                    if (!empty($pdoErrorMsg)) {
                        $result = false;
                        global $mysqlVersion;
                        try {
                            $mysqlVersion = \Includes\Utils\Database::getDbVersion();

                        } catch(\Exception $e) {
                            $mysqlVersion = '';
                        }

                        if (!empty($mysqlVersion)) {
                            $version = 'MySQL v' . $mysqlVersion . ': ';
                        }

                        ga_event('warning', 'dump_upload_failed', $version . $pdoErrorMsg);
                        break;
                    }

                    @unlink($dump_file);
                }
            }

            if ($result) {

                echo '<span class="status-ok">OK</span>';

                echo '<br /><p>' . xtr('Redirecting to the next step...') . '</p>';
?>

<script type="text/javascript">

function isProcessComplete() {

    if (document.getElementById('next-button')) {
        setNextButtonDisabled(false, true);
        setNextButtonDisabled(true);
        document.getElementById('back-button').disabled = 'disabled';

    } else {
        setTimeout('isProcessComplete()', 1000);
    }
}

window.onload = function () {
    setNextButtonDisabled(true);
}

setTimeout('isProcessComplete()', 1000);

</script>

<?php
            }
        }
    }


    if (!$result) {

        $result = doPrepareFixtures($params, $silentMode);

        if ($result) {

            doRemoveCache(null);

            $enabledModules = json_encode($lcSettings['enable_modules']);
?>

<div id="cache-rebuild-failed" class="cache-error" style="display: none;"><span><?php echo xtr('Oops!'); ?></span> <?php echo xtr('The current step of the cache rebuilding process is taking longer than expected. Check for possible problems <a href="https://kb.x-cart.com/pages/viewpage.action?pageId=7504578" target="_blank">here</a>.'); ?></div>

<?php
$params['auth_code'] = !empty($params['auth_code']) ? $params['auth_code'] : save_authcode($params);
?>

<iframe id="process_iframe" src="service.php?/install&<?php echo http_build_query(['modules' => $enabledModules, 'version' => LC_VERSION, 'auth_code' => $params['auth_code']]); ?>" width="100%" height="300" frameborder="0" marginheight="10" marginwidth="10"></iframe>

<?php echo '<div class="building-cache-notice">' . xtr('Building cache notice') . '</div>'; ?>

<script type="text/javascript">

    var errCount = 0;
    var currentStep = '0';
    var isStopped = false;

    function isProcessComplete() {

        var iframe = document.getElementById('process_iframe').contentWindow.document;

        if (iframe.getElementById('finish')) {
            resetCacheWindowContent();

        } else if (iframe.body.innerHTML) {
            var finishedStepsCount = iframe.getElementsByClassName('finished-step-info').length;

            if (currentStep !== finishedStepsCount) {
                errCount = 0;
                currentStep = finishedStepsCount;
                resetCacheRebuildFailure();
            }

            if (errCount > 60) {
                var currentStepInfo = iframe.querySelector('.in-progress-step-info div')
                    ? iframe.querySelector('.in-progress-step-info div').innerHTML
                    : '';
                processCacheRebuildFailure(currentStepInfo);
                isStopped = true;

            } else {
                errCount = errCount + 1;
            }

            setTimeout('isProcessComplete()', 1000);

        } else {
            setTimeout('isProcessComplete()', 1000);
        }
    }

    setTimeout('isProcessComplete()', 1000);

</script>

<?php

        } else {
            fatal_error(xtr('Error has encountered while creating fixtures or modules list.'), 'file', 'fixtures');
        }

        $error = true;
    }

    return false;
}

/**
 * Install_dirs module
 *
 * @param array $params
 *
 * @return bool
 */
function module_install_dirs(&$params)
{
    global $error, $lcSettings;

    $result = doUpdateConfig($params, false) && doUpdateMainHtaccess($params);

    if ($result) {

?>

<iframe id="process_iframe" src="install.php?target=install&action=dirs" width="100%" height="300" frameborder="0" marginheight="10" marginwidth="10"></iframe>

<?php

    }

?>

<script type="text/javascript">

    function isProcessComplete() {

        if (document.getElementById('process_iframe').contentWindow.document.getElementById('finish')) {
            document.getElementById('install-form').submit();

        } else {
            setTimeout('isProcessComplete()', 1000);
        }
    }

    setTimeout('isProcessComplete()', 1000);

</script>


<input type="hidden" name="ck_res" value="<?php echo intval($result); ?>" />

<?php

    if (is_null($params['new_installation'])) {

?>

        <input type="hidden" name="params[force_current]" value="<?php echo get_step('install_done'); ?>" />

<?php
    }

    $error = true;

    return false;
}


/**
 * Output form for gathering admi account data
 *
 * @param array $params
 *
 * @return bool
 */
function module_cfg_create_admin(&$params)
{
    global $error, $skinsDir;

    $paramFields = array(
        'login'             => array(
            'title'       => xtr('E-mail'),
            'description' => xtr('E-mail address of the store administrator'),
            'def_value'   => isset($params['_login']) ? $params['_login'] : '',
            'required'    => true,
            'type'        => 'text'
        ),
        'password'          => array(
            'title'       => xtr('Password'),
            'description' => '',
            'def_value'   => isset($params['_password']) ? $params['_password'] : '',
            'required'    => true,
            'type'        => 'password',
            'disableAutofill' => true,
        ),
    );

?>

<div>

<p class="text-left"><?php echo xtr('E-mail and password that you provide on this screen will be used to create primary administrator profile. Use them as credentials to access the Administrator Zone of your online store.'); ?></p>

<table width="100%" border="0" cellpadding="10">

<?php


    foreach ($paramFields as $fieldName => $fieldData) {
        displayFormElement($fieldName, $fieldData);
    }

?>

</table>

</div>

<div class="clear"></div>

<?php

    if (is_null($params["new_installation"])) {

?>

<input type="hidden" name="params[force_current]" value="<?php echo get_step("install_done")?>" />

<?php
    }
?>

<?php
}

/**
 * cfg_create_admin module "Next" button validator
 *
 * @return string
 */
function module_cfg_create_admin_js_next()
{
?>
    function step_next() {

        // validate login
        //
        if (!checkEmailAddress(document.getElementById('param-login'))) {
            return false;
        }

        // validate password and password confirm
        //
        if (document.ifrm.elements['params[password]'].value == "") {
            document.ifrm.elements['params[password]'].className += ' error';
            document.ifrm.elements['params[password]'].focus();
            alert('<?php echo xtr('Please, enter non-empty password'); ?>');
            return false;
        }

        return true;
    }
<?php
}


/**
 * Install_done module
 *
 * @param array $params
 *
 * @return bool
 */
function module_install_done(&$params)
{
    // if authcode IS NULL, then this is probably the first install, skip
    $checkParams = array('auth_code', 'login', 'password');

    $accountParams = true;

    // Check parameters for creating an administrator account
    foreach ($checkParams as $paramValue) {
        $accountParams = $accountParams && (isset($paramValue) && strlen(trim($paramValue)) > 0);
    }

?>

<div style="text-align: left;">

<?php
    // create/update admin account from the previous step
    if ($accountParams) {
        doCreateAdminAccount($params);
    }

    doFinishInstallation($params);

?>

</div>

<?php

    return false;
}

/**
 * End of Modules section
 */


/**
 * Log every request to install.php
 */
$_params = ('POST' == $_SERVER['REQUEST_METHOD'] ? $_POST : $_GET);

x_install_log(null, $_params);
