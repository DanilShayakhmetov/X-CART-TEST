<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Product\Details\Customer\Page;

/**
 * Abstract product page
 *
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage implements \XLite\Base\IDecorator
{
    /**
     * Process global tab addition into list
     *
     * @param                                  $list
     * @param \XLite\Model\Product\IProductTab $tab
     */
    protected function applyStaticTabListValue(&$list, $tab)
    {
        parent::applyStaticTabListValue($list, $tab);

        if ($tab->getServiceName() === 'Reviews') {
            $list[$tab->getServiceName()] = [
                'list'   => 'product.details.page.tab.reviews',
                'weight' => $tab->getPosition(),
            ];
        }
    }

    /**
     * Define whether to display the rating on the page
     *
     * @return boolean
     */
    public function isVisibleAverageRatingOnPage()
    {
        return true;
    }
}
