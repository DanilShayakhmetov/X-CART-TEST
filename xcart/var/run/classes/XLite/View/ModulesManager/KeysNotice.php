<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * License keys notice page
 *
 * @ListChild (list="admin.center", zone="admin", weight=0)
 */
class KeysNotice extends \XLite\View\ModulesManager\AModulesManager
{
    /**
     * Cached value of isCoreWarning() method
     *
     * @var boolean
     */
    protected $isCoreWarning = null;

    /**
     * Cahced list of unallowed modules
     *
     * @var array
     */
    protected $unallowedModules = null;

    /**
     * 'Purchase all' link URL
     *
     * @var string
     */
    protected $purchaseAllURL = null;

    /**
     * @var array
     */
    protected $xbProductIds = [];

    /**
     * Get list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'keys_notice';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }


    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'keys_notice';
    }

    /**
     * URL of the page where license can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite\Core\Marketplace::getBusinessPurchaseURL();
    }

    /**
     * URL of the X-Cart company's Contact Us page
     *
     * @return string
     */
    protected function getContactUsURL()
    {
        return \XLite\Core\Marketplace::getContactUsURL();
    }

    /**
     * Check if there are unallowed and enabled modules
     *
     * @return bool
     */
    protected function hasUnallowedEnabledModules()
    {
        return \XLite\Core\Marketplace::getInstance()->hasUnallowedModules();
    }

    /**
     * Get list of unallowed modules
     *
     * @return array
     */
    protected function getUnallowedModules()
    {
        if (!isset($this->unallowedModules)) {

            $result = array();

            $this->xbProductIds = array(
                'editions'        => array(),
                'addons'          => array(),
            );

            $list = \XLite\Core\Marketplace::getInstance()->getInactiveContentData();

            if ($list) {
                foreach ($list as $k => $module) {
                    $item = $this->preprocessUnallowedModule($module);
                    if ($item) {
                        $result[] = $item;
                    }
                }

                usort($result, array($this, 'sortUnallowedModules'));

                if (!empty($this->xbProductIds['editions'])) {
                    $editions = array_unique(
                        array_intersect(...$this->xbProductIds['editions'])
                        ?: array_merge(...$this->xbProductIds['editions'])
                    );

                    $this->xbProductIds['editions'] = [array_shift($editions)];
                }
            }

            $this->unallowedModules = $result;
        }

        return $this->unallowedModules;
    }

    /**
     * Method to sort list of modules in alphabet order
     *
     * @param array First module data
     * @param array Second module data
     *
     * @return int
     */
    public function sortUnallowedModules($m1, $m2)
    {
        return strcmp($m1['moduleName'], $m2['moduleName']);
    }

    /**
     * @param array $module
     *
     * @return
     */
    protected function preprocessUnallowedModule($module)
    {
        $result = $module;

        $message = '';
        $url = '';

        if (!empty($module['license'])) {
            $message = static::t('Inactive license key ({{key}})', array('key' => $module['license']));
            $url = $module['purchaseUrl'] ?: '';

        } else {
            $license = \XLite::getXCNLicense();
            $edition = null;

            $moduleEditions = [];
            foreach ($module['editions'] ?? [] as $moduleEdition) {
                if (preg_match('/^(\d+)_(.+)$/', $moduleEdition, $match)) {
                    $moduleEditions[$match[1]] = $match[2];
                }
            }

            if ($moduleEditions) {
                if (in_array('Free', $moduleEditions, true)) {

                    return null;
                }

                $keyData = $license['keyData'] ?? null;
                $edition = $keyData['edition'] ?? null;

                if (!in_array($edition, $moduleEditions, true)) {

                    $this->xbProductIds['editions'][] = array_keys($moduleEditions);

                    foreach ($moduleEditions as $editionId => $editionName) {
                        $moduleEditions[$editionId] = $editionName;
                    }

                    $list = '';
                    if (count($moduleEditions) === 1) {
                        $list = array_pop($moduleEditions);

                    } elseif (count($moduleEditions) > 1) {
                        $last = array_shift($moduleEditions);
                        $list = implode(', ', $moduleEditions) . ' ' . static::t('or') . ' ' . $last;
                    }

                    if ($edition) {
                        $message = static::t('Does not match license type (requires {{list}} edition)', array('list' => $list));

                    } else {
                        $message = static::t('Requires {{list}} edition', array('list' => $list));
                    }
                }

            } elseif ($module['price'] > 0) {
                $message = static::t('License key is missing');
                if ($module['purchaseUrl']) {
                    $url = $module['purchaseUrl'];
                    $this->xbProductIds['addons'][] = $module['xbProductId'];
                }
            }
        }

        $result['message'] = $message;
        $result['url'] = $url;

        return $result;
    }

    /**
     * Get URL for 'Remove unallowed modules' action
     *
     * @return string
     */
    protected function getRemoveModulesURL()
    {
        return \XLite::getInstance()->getShopURL('service.php?/removeUnallowedModules');
    }

    /**
     * Get URL for 'Back to Trial mode' action
     *
     * @return string
     */
    protected function getBackToTrialURL()
    {
        return $this->buildURL('module_key', 'unset_core_license');
    }

    /**
     * Get URL for 'Back to Trial mode' action
     *
     * @return string
     */
    protected function getRecheckURL()
    {
        return $this->buildURL(
            'keys_notice',
            'recheck',
            array(
                'returnUrl' => \XLite\Core\Request::getInstance()->returnUrl
            )
        );
    }

    /**
     * Get true if widget in core-license mode
     *
     * @return boolean
     */
    protected function isCoreWarning()
    {
        if (!isset($this->isCoreWarning)) {
            $this->isCoreWarning = $this->isDisplayBlockContent();
        }

        return $this->isCoreWarning;
    }

    /**
     * Return true if fraud status has been confirmed
     *
     * @return boolean
     */
    protected function isFraudStatusConfirmed()
    {
        $result = false;

        if (\XLite\Core\Marketplace::getInstance()->isFraud()) {
            $result = true;
            \XLite\Core\Session::getInstance()->fraudWarningDisplayed = true;
            \XLite\Core\Session::getInstance()->shouldDisableUnallowedModules = true;
        }

        return $result;
    }

    /**
     * Get 'Purchase all' button URL
     *
     * @return string
     */
    protected function getPurchaseAllURL()
    {
        if (!isset($this->purchaseAllURL)) {

            $urlParamsAggregated = [
                'action' => 'add_items'
            ];
            $i = 1;

            if ($this->xbProductIds) {
                foreach (array('editions', 'addons') as $licType) {
                    if (!empty($this->xbProductIds[$licType])) {
                        foreach ($this->xbProductIds[$licType] as $id) {
                            $urlParamsAggregated['xbid_' . $i] = $id;
                            $i ++;
                        }
                    }
                }
            }

            if ($this->isCoreWarning()) {
                $license = $this->getCoreLicense();
                if ($license && !empty($license['xbProductId'])) {
                    $urlParamsAggregated['xbid_' . $i] = $license['xbProductId'];
                }
            }

            $this->purchaseAllURL = $urlParamsAggregated
                ? \XLite\Core\Marketplace::getPurchaseURL(null, $urlParamsAggregated, true)
                : '';
        }

        return $this->purchaseAllURL;
    }

    /**
     * Return true if 'Purchase all' button should be displayed
     *
     * @return boolean
     */
    protected function isDisplayPurchaseAllButton()
    {
        return (bool)$this->getPurchaseAllURL();
    }

    /**
     * Get currently activated core license data
     *
     * @return array
     */
    protected function getCoreLicense()
    {
        if (!isset($this->coreLicense)) {

            $result = array();

            $license = \XLite::getXCNLicense();

            if ($license) {
                $keyData = $license['keyData'];
                $xbProductId = $keyData['xbProductId'];
                $xbProductId = (int) $xbProductId;

                $result['title'] = 'X-Cart ' . $keyData['editionName'];
                $result['message'] = static::t('Inactive license key ({{key}})', array('key' => $license['keyValue']));
                if (0 < $xbProductId) {
                    $result['url'] = \XLite\Core\Marketplace::getPurchaseURL($xbProductId);
                    $result['xbProductId'] = $xbProductId;
                }
            }

            $this->coreLicense = $result;
        }

        return $this->coreLicense;
    }
}
