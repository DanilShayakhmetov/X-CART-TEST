<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View;

use Includes\Utils\Module\Manager;

/**
 * Add2CartPopup module settings page widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Admin extends \XLite\View\Dialog
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'add2_cart_popup';

        return $result;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Add2CartPopup';
    }

    /**
     * Get promotion message
     *
     * @return string
     */
    protected function getPromotionMessage()
    {
        $addons  = $this->getAddons();
        $modules = [];

        foreach ($addons as $addon => $title) {
            list($author, $name) = explode('\\', $addon);

            if (!$author || !$name || Manager::getRegistry()->isModuleEnabled($author, $name)) {
                continue;
            }

            $url = Manager::getRegistry()->getModuleServiceURL($author, $name);

            $modules[] = '<a href="' . $url . '">' . $title . '</a>';
        }

        return (0 < count($modules))
            ? static::t('Install additional modules to add more product sources', ['list' => implode(', ', $modules)])
            : '';
    }

    /**
     * Get modules list which provide additional products sources for 'Add to Cart Popup' dialog
     *
     * @return array
     */
    protected function getAddons()
    {
        return [
            'XC\Upselling'        => 'Related Products',
            'CDev\ProductAdvisor' => 'Product Advisor',
        ];
    }
}
