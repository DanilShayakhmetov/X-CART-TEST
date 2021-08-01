<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use Includes\Utils\Module\Module;

/**
 * Automate shipping routine page view
 */
class AutomateShippingRoutine extends \XLite\View\AView
{
    const PROPERTY_VALUE_YES    = 'Y';
    const PROPERTY_VALUE_NO     = 'N';
    const PROPERTY_VALUE_APP_TYPE_CLOUD         = 'C';
    const PROPERTY_VALUE_APP_TYPE_WINDOWS       = 'W';

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('automate_shipping_routine'));
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'automate_shipping_routine/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'automate_shipping_routine/style.css';

        return $list;
    }

    /**
     * Get shipping modules
     * 
     * @return string
     */
    protected function getShippingModulesLink()
    {
        return \XLite::getInstance()->getServiceURL('#/available-addons', null, ['tag' => 'Shipping']);
    }

    /**
     * Get shipping modules list
     * 
     * @return array
     */
    protected function getShippingModules()
    {
        $marketplaceModuleIds = [
            'Qualiteam-ShippingEasy',
            'ShipStation-Api',
        ];
        
        $modules = \XLite\Core\Marketplace::getInstance()->getAutomateShippingRoutineModules($marketplaceModuleIds);

        $modules[] = [
            'name' => 'ShipWorks',
            'moduleName' => 'ShipWorks',
            'link' => 'http://www.shipworks.com/integrations/xcart/?source=si10049347',
        ];

        return array_filter($modules, function ($module) {
            return isset($module['moduleName']) && isset($module['name']);
        });
    }

    /**
     * Get module name
     *
     * @param  mixed $module
     * @return string
     */
    public function getModuleName($module)
    {
        return $module['moduleName'];
    }

    /**
     * @param $module
     *
     * @return bool|string
     */
    protected function getModuleButtonLink($module)
    {
        if (isset($module['link'])) {
            return $module['link'];
        }

        if (isset($module['id'])) {
            if ($module['installed'] && $module['enabled']) {
                if ($this->getSettingsForm($module)) {
                    return $this->getSettingsForm($module);
                }
            } else {
                return \Includes\Utils\Module\Manager::getRegistry()->getModuleServiceURL($module['author'], $module['name']);
            }
        }

        return '';
    }

    /**
     * @param $module
     *
     * @return string
     */
    protected function getModuleButtonTitle($module)
    {
        if (isset($module['link'])) {
            return static::t('Install');
        }

        if (isset($module['id'])) {
            if ($module['installed'] && $module['enabled']) {
                if ($this->getSettingsForm($module)) {
                    return static::t('Settings');
                }
            } else {
                return static::t('Install');
            }
        }

        return '';
    }

    /**
     * @param $module
     *
     * @return string
     */
    protected function getModuleButtonStyle($module)
    {
        $settingsButtonStyle = 'regular-button';
        $installButtonStyle = 'regular-main-button';

        if (isset($module['link'])) {
            return $installButtonStyle;
        }

        if (isset($module['id'])) {
            if ($module['installed'] && $module['enabled']) {
                if ($this->getSettingsForm($module)) {
                    return $settingsButtonStyle;
                }
            } else {
                return $installButtonStyle;
            }
        }

        return '';
    }

    /**
     * @param $module
     *
     * @return bool
     */
    protected function isModuleAvailable($module)
    {
        return isset($module['id']) || isset($module['link']);
    }

    /**
     * Get shipping module properties list
     * 
     * @return array
     */
    protected function getShippingModuleProperties()
    {
        return array(
            'common' => array(
                'labels'    => static::t('Print Shipping labels'),
                'trial'     => static::t('FREE trial'),
            ),
            'integrations' => array(
                'ebay'      => static::t('eBay'),
                'amazon'    => static::t('Amazon'),
                'etsy'      => static::t('ETSY'),
                'stamps'    => static::t('Stamps.com'),
                'fedex'     => static::t('FedEx'),
                'ups'       => static::t('UPS'),
                'usps'      => static::t('USPS'),
                'dhl'       => static::t('DHL'),
            ),
            'app' => array(
                'type' => static::t('App type')
            ),
        );
    }

    /**
     * Get shipping module property value
     *
     * @return array
     */
    protected function getShippingModulePropertyDictionary()
    {
        return array(
            'ShippingEasy'  => array(
                'common' => array(
                    'labels'    => static::PROPERTY_VALUE_YES,
                    'trial'     => static::PROPERTY_VALUE_YES,
                    'refunds'   => static::PROPERTY_VALUE_NO,
                ),
                'integrations' => array(
                    'ebay'      => static::PROPERTY_VALUE_YES,
                    'amazon'    => static::PROPERTY_VALUE_YES,
                    'etsy'      => static::PROPERTY_VALUE_YES,
                    'stamps'    => static::PROPERTY_VALUE_NO,
                    'fedex'     => static::PROPERTY_VALUE_YES,
                    'ups'       => static::PROPERTY_VALUE_YES,
                    'usps'      => static::PROPERTY_VALUE_YES,
                    'dhl'       => static::PROPERTY_VALUE_YES,
                ),
                'app' => array(
                    'type' => static::PROPERTY_VALUE_APP_TYPE_CLOUD,
                ),
            ),
            'Api'  => array(
                'common' => array(
                    'labels'    => static::PROPERTY_VALUE_YES,
                    'trial'     => static::PROPERTY_VALUE_YES,
                    'refunds'   => static::PROPERTY_VALUE_NO,
                ),
                'integrations' => array(
                    'ebay'      => static::PROPERTY_VALUE_YES,
                    'amazon'    => static::PROPERTY_VALUE_YES,
                    'etsy'      => static::PROPERTY_VALUE_YES,
                    'stamps'    => static::PROPERTY_VALUE_YES,
                    'fedex'     => static::PROPERTY_VALUE_YES,
                    'ups'       => static::PROPERTY_VALUE_YES,
                    'usps'      => static::PROPERTY_VALUE_YES,
                    'dhl'       => static::PROPERTY_VALUE_YES,
                ),
                'app' => array(
                    'type' => static::PROPERTY_VALUE_APP_TYPE_CLOUD,
                ),
            ),
            'ShipWorks'  => array(
                'common' => array(
                    'labels'    => static::PROPERTY_VALUE_YES,
                    'trial'     => static::PROPERTY_VALUE_YES,
                    'refunds'   => static::PROPERTY_VALUE_NO,
                ),
                'integrations' => array(
                    'ebay'      => static::PROPERTY_VALUE_YES,
                    'amazon'    => static::PROPERTY_VALUE_YES,
                    'etsy'      => static::PROPERTY_VALUE_YES,
                    'stamps'    => static::PROPERTY_VALUE_YES,
                    'fedex'     => static::PROPERTY_VALUE_YES,
                    'ups'       => static::PROPERTY_VALUE_YES,
                    'usps'      => static::PROPERTY_VALUE_YES,
                    'dhl'       => static::PROPERTY_VALUE_YES,
                ),
                'app' => array(
                    'type' => static::PROPERTY_VALUE_APP_TYPE_WINDOWS,
                ),
            ),
        );
    }

    /**
     * Get properties group label
     * 
     * @return string
     */
    protected function getGroupLabel($groupKey)
    {
        return 'integrations' === $groupKey
            ? strtoupper(static::t('Integration with'))
            : '';
    }

    /**
     * Get module logo
     * 
     * @param array $module Module
     * 
     * @return boolean
     */
    protected function getImageURL($module)
    {
        $name = $module['name'];
        $path = sprintf('automate_shipping_routine/images/%s_logo.png', strtolower($name));

        $modulePublicIcon = isset($module['icon'])
            ? $module['icon']
            : '';

        return \XLite\Core\Layout::getInstance()->getResourceWebPath($path) ?: $modulePublicIcon;
    }

    /**
     * Check if module has settings form
     * 
     * @param array $module Module
     * 
     * @return boolean
     */
    protected function getSettingsForm($module)
    {
        return isset($module['id'])
            ? Module::callMainClassMethod($module['id'], 'getSettingsForm')
            : null;
    }

    /**
     * Get shipping module property template by value
     *
     * @param string $value Value of property
     * 
     * @return array
     */
    protected function getPropertyTemplate($value)
    {
        $template = '';

        switch ($value) {
            case static::PROPERTY_VALUE_YES:
                $template = 'automate_shipping_routine/parts/property_yes.twig';
                break;
            case static::PROPERTY_VALUE_NO:
                $template = 'automate_shipping_routine/parts/property_no.twig';
                break;
            case static::PROPERTY_VALUE_APP_TYPE_CLOUD:
            case static::PROPERTY_VALUE_APP_TYPE_WINDOWS:
                $template = 'automate_shipping_routine/parts/property_app_type.twig';
                break;
        }

        return $template;
    }

    /**
     * Get shipping module property icon by value
     *
     * @param string $value Value of property
     * 
     * @return array
     */
    protected function getAppTypeIcon($value)
    {
        return $value == static::PROPERTY_VALUE_APP_TYPE_CLOUD
            ? 'fa-cloud'
            : 'fa-windows';
    }

    /**
     * Get shipping module property icon by value
     *
     * @param string $value Value of property
     * 
     * @return array
     */
    protected function getAppTypeText($value)
    {
        return $value == static::PROPERTY_VALUE_APP_TYPE_CLOUD
            ? static::t('Cloud Service')
            : static::t('Win app');
    }

    /**
     * Get shipping module property value
     * 
     * @param array $module Module
     * @param string $property Property key
     * 
     * @return string
     */
    protected function getShippingModulePropertyValue($module, $type, $property)
    {
        $name = $module['name'];

        $dict = $this->getShippingModulePropertyDictionary();
        $moduleTypeDict = $dict[$name][$type];

        return $moduleTypeDict[$property];
    }

    // }}}
}
