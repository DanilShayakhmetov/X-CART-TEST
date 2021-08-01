<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Module
 */
abstract class AModule
{
    /**
     * Keys for  moveClassesInLists and moveTemplatesInLists methods
     */
    const TO_DELETE = 'to_delete';
    const TO_ADD    = 'to_add';

    const MODULE_TYPE_CUSTOM_MODULE = 0x1;
    const MODULE_TYPE_PAYMENT       = 0x2;
    const MODULE_TYPE_SKIN          = 0x4;
    const MODULE_TYPE_SHIPPING      = 0x8;

    /**
     * @var Module[]
     */
    static protected $moduleData = [];

    /**
     * @return string
     */
    public static function getId()
    {
        return Module::getModuleIdByClassName(static::class);
    }

    /**
     * @return Module
     */
    public static function getModuleData()
    {
        $id = static::getId();
        if (!isset(static::$moduleData[$id])) {
            static::$moduleData[$id] = Manager::getRegistry()->getModule($id);
        }

        return static::$moduleData[$id];
    }

    /**
     * Method to initialize concrete module instance
     *
     * @return void
     */
    public static function init()
    {
        // Register skins into Layout
        static::registerSkins();

        // Register image sizes
        static::registerImageSizes();
    }

    /**
     * Update view list entries
     */
    public static function updateViewListEntries()
    {
        static::manageClasses();
        static::manageTemplates();
    }

    /**
     * Return link to the module author page
     *
     * @return string
     */
    public static function getAuthorPageURL()
    {
        return '';
    }

    /**
     * Return link to the module page
     *
     * @return string
     */
    public static function getPageURL()
    {
        return '';
    }

    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return static::getModuleData()->type === 'payment' ? static::getPaymentSettingsForm() : null;
    }

    /**
     * Defines the link for the payment settings form
     *
     * @return string
     */
    public static function getPaymentSettingsForm()
    {
        return null;
    }

    /**
     * Return module dependencies
     *
     * @return array
     * @deprecated
     */
    public static function getDependencies()
    {
        return array();
    }

    /**
     * Return list of mutually exclusive modules
     *
     * @return array
     * @deprecated
     */
    public static function getMutualModulesList()
    {
        return array();
    }

    /**
     * Get module version
     *
     * @return string
     */
    public static function getVersion()
    {
        return static::getModuleData()->version;
    }

    /**
     * Check - module required disabled+redeploy+uninstall (true) or deploy+uninstall (false)
     *
     * @return boolean
     */
    public static function isSeparateUninstall()
    {
        return false;
    }

    /**
     * Get the module skins list to register in layout.
     * The array has the following format:
     *
     * return array(
     *  <interface_name> => array(
     *  <skin_short_path1>,
     * ...
     * ),
     * ...
     * )
     *
     * Interface in this list:
     *
     * \XLite::ADMIN_INTERFACE
     * \XLite::CONSOLE_INTERFACE
     * \XLite::COMMON_INTERFACE
     * \XLite::MAIL_INTERFACE
     * \XLite::CUSTOMER_INTERFACE
     *
     * <skin_short_path> - Relative skin path inside the LC_DIR_SKINS directory:
     *
     * For directory `<application_dir>/skins/my_module_skin` short path value will be 'my_module_skin'
     *
     * @return array
     */
    public static function getSkins()
    {
        return static::getModuleData() ? static::getModuleData()->skins : [];
    }

    /**
     * You can define some special conditions to register or not your own skins (defined in static::getSkins() method)
     *
     * By default the TRUE condition is defined
     *
     * @see static::getSkins()
     * @see static::registerSkins()
     *
     * @return boolean
     */
    public static function doRegisterSkins()
    {
        return true;
    }

    /**
     * Skins registration method.
     * Do not change it until you are not sure.
     *
     * @return void
     */
    public static function registerSkins()
    {
        if (static::doRegisterSkins()) {
            foreach (static::getSkins() as $interface => $skinsToRegister) {
                foreach ($skinsToRegister as $skin) {
                    static::registerSkin($skin, $interface);
                }
            }
        }
    }

    /**
     * Make one skin entry registration to provide a flexible skin registration
     *
     * @param string $skin      Skin name
     * @param string $interface Interface code
     *
     * @return void
     */
    public static function registerSkin($skin, $interface)
    {
        \XLite\Core\Layout::getInstance()->addSkin($skin, $interface);
    }

    /**
     * Returns image sizes
     *
     * @return array
     */
    public static function getImageSizes()
    {
        return [];
    }

    /**
     * Register image sizes
     *
     * If you want to change existing image sizes only once, on module install
     * you should add a record to install.yaml of your module:
     *
     * For example:
     *
     * XLite\Model\ImageSettings:
     *   - { model: XLite\Model\Image\Product\Image, code: Default, width: 123, height: 321 }
     *   - { model: XLite\Model\Image\Category\Image, code: Default, width: 456, height: 654 }
     *
     * @return void
     */
    public static function registerImageSizes()
    {
        $sizes = static::getImageSizes();

        if ($sizes) {
            \XLite\Logic\ImageResize\Generator::addImageSizes($sizes);
        }
    }

    /**
     * Move viewers method registers widgets for moving or removing between the lists
     * The module must provide the array of records with the following formats:
     *
     * array(
     *   '\XLite\View\Field' => array(      // Name of the viewer class
     *      array('from_list', 'admin'),    // From list "from_list" and "admin" zone
     *      array('to_list', '10', 'admin') // To list "to_list" with weight = 10 and zone = "admin"
     *   ),
     *   '\XLite\View\Field' => array(
     *      'from_list',                    // From list "from_list"
     *      array('to_list', '10', 'admin') // To list "to_list" with weight = 10 and zone = "admin"
     *   ),
     *   '\XLite\View\Field' => array(
     *      'from_list',                    // From list "from_list"
     *      'to_list'                       // To list "to_list" , with default parameters
     *   ),
     *   '\XLite\View\Field' => array(
     *      array('from_list2', 'admin'),   // Remove from list "from_list2" and "admin" zone
     *   ),
     *   '\XLite\View\Field' => array(
     *      'from_list',                    // Remove from list "from_list"
     *   ),
     * )
     *
     * If you need to add or remove several entries you can use the static::TO_DELETE and static::TO_ADD keys:
     *
     * '\XLite\View\Field1' => array(
     *      static::TO_DELETE => array(
     *          array('product.inventory.parts1', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *          array('product.inventory.parts2', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *      ),
     *      static::TO_ADD => array(
     *          array('product.inventory.parts3', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *          array('product.inventory.parts4', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *      ),
     * ),
     * '\XLite\View\Field2' => array(
     *      static::TO_DELETE => array(
     *          array('product.inventory.parts', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *      ),
     * )
     *
     * If the TO_DELETE/TO_ADD keys are used the other info will not be considered
     *
     * @return array
     */
    protected static function moveClassesInLists()
    {
        return [];
    }

    /**
     * Move templates method registers templates for moving or removing between the lists
     * The module must provide the array of records with the following formats:
     *
     * array(
     *   'field/body.twig' => array(         // Name of the template
     *      array('from_list', 'admin'),    // From list "from_list" and "admin" zone
     *      array('to_list', '10', 'admin') // To list "to_list" with weight = 10 and zone = "admin"
     *   ),
     *   'field/body.twig' => array(
     *      'from_list',                    // From list "from_list"
     *      array('to_list', '10', 'admin') // To list "to_list" with weight = 10 and zone = "admin"
     *   ),
     *   'field/body.twig' => array(
     *      'from_list',                    // From list "from_list"
     *      'to_list'                       // To list "to_list" , with default parameters
     *   ),
     *   'field/body.twig' => array(
     *      array('from_list2', 'admin'),   // Remove from list "from_list2" and "admin" zone
     *   ),
     *   'field/body.twig' => array(
     *      'from_list',                    // Remove from list "from_list"
     *   ),
     * )
     *
     * If you need to add or remove several entries you can use the static::TO_DELETE and static::TO_ADD keys:
     *
     * 'product/inventory/inv_track_amount.twig' => array(
     *      static::TO_DELETE => array(
     *          array('product.inventory.parts1', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *          array('product.inventory.parts2', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *      ),
     *      static::TO_ADD => array(
     *          array('product.inventory.parts3', 100, \XLite\Model\ViewList::INTERFACE_ADMIN),
     *          array('product.inventory.parts4', 200, \XLite\Model\ViewList::INTERFACE_ADMIN),
     *      ),
     * ),
     * 'product/inventory/inv_track_selector.twig' => array(
     *      static::TO_DELETE => array(
     *          array('product.inventory.parts', \XLite\Model\ViewList::INTERFACE_ADMIN),
     *      ),
     * )
     *
     * If the TO_DELETE/TO_ADD keys are used the other info will not be considered
     *
     * @return array
     */
    protected static function moveTemplatesInLists()
    {
        return [];
    }

    /**
     * Common management method to move/remove widgets
     *
     * @param string $getter        Getter routine
     * @param string $removeRoutine Method name of the layout to remove the widgets from lists
     * @param string $addRoutine    Method name of the layout to add the widgets to lists
     *
     * @return void
     */
    protected static function manageWidgets($getter, $removeRoutine, $addRoutine)
    {
        $layout = \XLite\Core\Layout::getInstance();
        foreach (call_user_func('static::' . $getter) as $name => $params) {
            $toSet   = [];
            $fromSet = [];

            if (isset($params[static::TO_ADD]) || isset($params[static::TO_DELETE])) {
                $fromSet = isset($params[static::TO_DELETE]) ? $params[static::TO_DELETE] : [];
                $toSet   = isset($params[static::TO_ADD]) ? $params[static::TO_ADD] : [];

            } elseif (count($params) === 1) {
                // Remove case
                $fromSet = [is_array($params[0]) ? $params[0] : [$params[0]]];

            } else {
                // Move widgets case
                $fromSet = [is_array($params[0]) ? $params[0] : [$params[0]]];
                $toSet   = [is_array($params[1]) ? $params[1] : [$params[1]]];
            }

            foreach ($fromSet as $from) {
                $layout->{$removeRoutine}($name, $from[0], isset($from[1]) ? $from[1] : null);
            }

            foreach ($toSet as $to) {
                $toParams = [];
                if (isset($to[1])) {
                    $toParams['weight'] = $to[1];
                }

                if (isset($to[2])) {
                    $toParams['zone'] = $to[2];
                }

                $layout->{$addRoutine}($name, $to[0], $toParams);
            }
        }
    }

    /**
     * Manage viewer classes routine
     *
     * @return void
     */
    protected static function manageClasses()
    {
        static::manageWidgets('moveClassesInLists', 'removeClassFromList', 'addClassToList');
    }

    /**
     * Manage templates routine
     *
     * @return void
     */
    protected static function manageTemplates()
    {
        static::manageWidgets('moveTemplatesInLists', 'removeTemplateFromList', 'addTemplateToList');
    }

    /**
     * Returns an EventDispatcherInterface implementation to use in AModule::init to register event listeners/subscribers.
     *
     * @return EventDispatcherInterface
     * @throws ContainerException
     */
    protected static function getEventDispatcher()
    {
        return self::getContainer()->get('event_dispatcher');
    }

    /**
     * Gets the container.
     *
     * @return ContainerInterface  A ContainerInterface instance
     */
    private static function getContainer()
    {
        return \XLite::getInstance()->getContainer();
    }
}
