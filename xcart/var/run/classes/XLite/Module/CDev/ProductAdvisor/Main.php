<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Product labels keys
     */
    const PA_MODULE_PRODUCT_LABEL_NEW  = 'orange new-arrival';
    const PA_MODULE_PRODUCT_LABEL_SOON = 'grey coming-soon';

    /**
     * Default number of days during which the products are classified as new arrivals
     * (applied when the corresponding option is empty to detect if product should be marked with 'New!' label or no)
     */
    const PA_MODULE_OPTION_DEFAULT_DAYS_OFFSET = 30;

    /**
     * Name of cookie to store recently viewed products IDs
     */
    const LC_RECENTLY_VIEWED_COOKIE_NAME = 'rv';

    /**
     * TTL of cookie to store recently viewed products IDs
     */
    const LC_RECENTLY_VIEWED_COOKIE_TTL = 0;

    /**
     * Get the "New!" label
     *
     * @param \XLite\Model\Product $product Current product
     *
     * @return array
     */
    public static function getLabels(\XLite\Model\Product $product)
    {
        $result = [];

        if ($product->isNewProduct()
            && \XLite\Module\CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isCatalogEnabled(
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_mark_with_label
            )
        ) {
            $result[self::PA_MODULE_PRODUCT_LABEL_NEW] = \XLite\Core\Translation::getInstance()->translate('New!');
        }

        if ($product->isUpcomingProduct()
            && \XLite\Module\CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isCatalogEnabled(
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_mark_with_label
            )
        ) { 
            
            $result[self::PA_MODULE_PRODUCT_LABEL_SOON]
                = \XLite\Core\Translation::getInstance()->translate('Expected on X', 
                array('date' => \XLite\Core\Converter::getInstance()->
                        formatDate(
                            $product->getArrivalDate()
                        )
                     )
                     );
                
        }
        
        return $result;
    }

    /**
     * Get the "New!" label
     *
     * @param \XLite\Model\Product $product Current product
     *
     * @return array
     */
    public static function getProductPageLabels(\XLite\Model\Product $product)
    {
        $result = [];

        if ($product->isNewProduct()
            && \XLite\Module\CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isProductPageEnabled(
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_mark_with_label
            )
        ) {
            $result[self::PA_MODULE_PRODUCT_LABEL_NEW] = \XLite\Core\Translation::getInstance()->translate('New!');
        }

        return $result;
    }

    /**
     * Get array of recently viewed product IDs
     *
     * @return array
     */
    public static function getProductIds()
    {
        $result = [];

        $productIdsString = \XLite\Core\Request::getInstance()->{self::LC_RECENTLY_VIEWED_COOKIE_NAME};

        if ($productIdsString) {
            $productIds = explode('-', $productIdsString);
            $result     = array_unique(array_map('intval', $productIds), SORT_NUMERIC);

            $key = array_search(0, $result);

            if (false !== $key) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    /**
     * Save array of recently viewed product IDs
     *
     * @param integer $productId Integer Product ID value to add
     *
     * @return string
     */
    public static function saveProductIds($productId)
    {
        $result = false;

        if (0 < (int) $productId) {
            if (\XLite\Core\Request::getInstance()->isBot()) {
                \XLite\Core\Request::getInstance()->unsetCookie(
                    static::LC_RECENTLY_VIEWED_COOKIE_NAME
                );

            } else {
                $result = static::getProductIds();
                array_unshift($result, (int) $productId);

                $result = array_unique($result, SORT_NUMERIC);
                $result = implode('-', $result);
                \XLite\Core\Request::getInstance()->setCookie(
                    static::LC_RECENTLY_VIEWED_COOKIE_NAME,
                    $result,
                    static::LC_RECENTLY_VIEWED_COOKIE_TTL
                );
            }
        }

        return $result;
    }

    /**
     * Returns offset (in days) to calculate new arrivals products
     *
     * @return integer
     */
    public static function getNewArrivalsOffset()
    {
        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_max_days
            ?: static::PA_MODULE_OPTION_DEFAULT_DAYS_OFFSET;
    }
}
