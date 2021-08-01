<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\ItemsList\Model;

/**
 * @ListChild (list="crud.paypalbutton.formHeader", zone="admin", weight="100")
 */
class PaypalButton extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Types
     */
    const TYPE_PRODUCT_PAGE = 'product_page';
    const TYPE_PRODUCT_LIST = 'product_list';
    const TYPE_CART         = 'cart';
    const TYPE_MINI_CART    = 'mini_cart';
    const TYPE_CHECKOUT     = 'checkout';
    const TYPE_CREDIT       = 'credit';

    /**
     * Cached list
     *
     * @var   array
     */
    protected $cachedList;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['paypal_button', 'paypal_commerce_platform_button']);
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'location'     => [
                static::COLUMN_MAIN    => true,
                static::COLUMN_NAME    => static::t('Location'),
                static::COLUMN_ORDERBY => 100,
            ],
            'size'         => [
                static::COLUMN_NAME    => static::t('Size'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\Paypal\View\FormField\Inline\ButtonSize',
                static::COLUMN_ORDERBY => 200,
            ],
            'color'        => [
                static::COLUMN_NAME    => static::t('Color'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\Paypal\View\FormField\Inline\ButtonColor',
                static::COLUMN_ORDERBY => 300,
            ],
            'shape'        => [
                static::COLUMN_NAME    => static::t('Shape'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\Paypal\View\FormField\Inline\ButtonShape',
                static::COLUMN_ORDERBY => 400,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return null;
    }

    /**
     * Return entities list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if (null === $this->cachedList) {
            $this->cachedList = [];
            foreach ($this->getPlainData() as $id => $cell) {
                $this->cachedList[] = new \XLite\Module\CDev\Paypal\Model\PaypalButtonCell(['id' => $id] + $cell);
            }
        }

        return $countOnly ? count($this->cachedList) : $this->cachedList;
    }

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        $result = [
            static::TYPE_PRODUCT_PAGE => [
                'location'     => static::t('pp-button-location:Product page'),
                'size'         => $this->getStyleValue(static::TYPE_PRODUCT_PAGE, 'size'),
                'color'        => $this->getStyleValue(static::TYPE_PRODUCT_PAGE, 'color'),
                'shape'        => $this->getStyleValue(static::TYPE_PRODUCT_PAGE, 'shape'),
            ],
            static::TYPE_PRODUCT_LIST => [
                'location'     => static::t('pp-button-location:Product list'),
                'size'         => $this->getStyleValue(static::TYPE_PRODUCT_LIST, 'size'),
                'color'        => $this->getStyleValue(static::TYPE_PRODUCT_LIST, 'color'),
                'shape'        => $this->getStyleValue(static::TYPE_PRODUCT_LIST, 'shape'),
            ],
            static::TYPE_CART         => [
                'location'     => static::t('pp-button-location:Cart'),
                'size'         => $this->getStyleValue(static::TYPE_CART, 'size'),
                'color'        => $this->getStyleValue(static::TYPE_CART, 'color'),
                'shape'        => $this->getStyleValue(static::TYPE_CART, 'shape'),
            ],
            static::TYPE_MINI_CART    => [
                'location'     => static::t('pp-button-location:Minicart'),
                'size'         => $this->getStyleValue(static::TYPE_MINI_CART, 'size'),
                'color'        => $this->getStyleValue(static::TYPE_MINI_CART, 'color'),
                'shape'        => $this->getStyleValue(static::TYPE_MINI_CART, 'shape'),
            ],
            static::TYPE_CHECKOUT     => [
                'location'     => static::t('pp-button-location:Checkout'),
                'size'         => $this->getStyleValue(static::TYPE_CHECKOUT, 'size'),
                'color'        => $this->getStyleValue(static::TYPE_CHECKOUT, 'color'),
                'shape'        => $this->getStyleValue(static::TYPE_CHECKOUT, 'shape'),
            ],
            static::TYPE_CREDIT       => [
                'location'     => static::t('pp-button-location:Checkout (credit)'),
                'size'         => $this->getStyleValue(static::TYPE_CREDIT, 'size'),
                'color'        => $this->getStyleValue(static::TYPE_CREDIT, 'color'),
                'shape'        => $this->getStyleValue(static::TYPE_CREDIT, 'shape'),
            ],
        ];

        if ($this->isPaypalForMarketplaces()) {
            $result = [
                static::TYPE_CHECKOUT => $result[static::TYPE_CHECKOUT]
            ];
        }

        return $result;
    }

    /**
     * @param $type
     * @param $field
     *
     * @return mixed
     */
    protected function getStyleValue($type, $field)
    {
        $configVariable = $type . '_style_' . $field;

        return \XLite\Core\Config::getInstance()->CDev->Paypal->{$configVariable};
    }

    /**
     * Update entities
     *
     * @return void
     */
    protected function updateEntities()
    {
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * @return bool
     */
    protected function isPaypalForMarketplaces()
    {
        return $this->getPaymentMethod()
               && $this->getPaymentMethod()->getServiceName() === \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM;
    }
}
