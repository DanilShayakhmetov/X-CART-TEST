<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View\Product\Details\Customer\Page;

/**
 * APage
 */
 class APage extends \XLite\Module\CDev\Sale\View\Details implements \XLite\Base\IDecorator
{
    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $labels = parent::getLabels();

        $labels += \XLite\Module\CDev\ProductAdvisor\Main::getProductPageLabels($this->getProduct());

        return $labels;
    }

    /**
     * Return coming soon label
     *
     * @return array
     */
    protected function getComingSoonLabel()
    {
        return [
            \XLite\Module\CDev\ProductAdvisor\Main::PA_MODULE_PRODUCT_LABEL_SOON => \XLite\Core\Translation::getInstance()->translate(
                'Expected on X',
                ['date' => \XLite\Core\Converter::getInstance()->formatDate($this->getProduct()->getArrivalDate())]
            )
        ];
    }

    /**
     * @return bool
     */
    protected function isShowComingSoonLabel()
    {
        return $this->getProduct()->isUpcomingProduct()
            && \XLite\Module\CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isProductPageEnabled(
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_mark_with_label
            );
    }
}