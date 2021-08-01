<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Viewer
 */
abstract class Price extends \XLite\Module\CDev\Sale\View\PriceMarketPrice implements \XLite\Base\IDecorator
{
    const SALE_PRICE_LABEL = 'sale_price_label';

    protected $salePriceLabel = null;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Sale/css/lc.less';

        return $list;
    }

    /**
     * Calculate "Sale percent off" value.
     *
     * @return integer
     */
    protected function getSalePercent()
    {
        $oldPrice = $this->getOldPrice();

        return 0 < $oldPrice
            ? round((1 - $this->getListPrice() / $oldPrice ) * 100)
            : 0;
    }

    /**
     * Return sale percent value
     *
     * @return float
     */
    protected function getSalePriceDifference()
    {
        return $this->getOldPrice() - $this->getListPrice();
    }

    /**
     * Return old price value
     *
     * @return float
     */
    protected function getOldPrice()
    {
        return \XLite::getInstance()->getCurrency()->roundValue(
            $this->getProduct()->getDisplayPriceBeforeSale()
        );
    }

    /**
     * Return old price value without possible market price
     *
     * @return float
     */
    protected function getPureOldPrice()
    {
        $oldPrice = \XLite\Module\CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this->getProduct(), 'getNetPriceBeforeSale', ['taxable'], 'display');

        return \XLite::getInstance()->getCurrency()->roundValue($oldPrice);
    }

    /**
     * Return sale participation flag
     *
     * @return boolean
     */
    protected function participateSale()
    {
        return $this->getListPrice() < $this->getPureOldPrice();
    }

    /**
     * Return the "x% label" element
     *
     * @return array
     */
    protected function getLabels()
    {
        return parent::getLabels() + [$this->getSalePriceLabel()];
    }

    /**
     * Return the specific sale price label info
     *
     * @return array
     */
    public function getSalePriceLabel()
    {
        if (!isset($this->salePriceLabel)) {
            if ($this->participateSale()) {
                $percent = sprintf(\XLite\Core\Config::getInstance()->Units->percent_format, $this->getSalePercent());

                $label = static::t('percent X off', ['percent' => $percent]);
                $this->salePriceLabel = [
                    'green sale-price' => $label,
                ];

                \XLite\Module\CDev\Sale\Core\Labels::addLabel($this->getProduct(), $this->salePriceLabel);
            }
        }

        return $this->salePriceLabel;
    }

    /**
     * Return the specific label info
     *
     * @param string $labelName
     *
     * @return array
     */
    protected function getLabel($labelName)
    {
        if (static::SALE_PRICE_LABEL === $labelName) {
            $result = $this->getSalePriceLabel();

        }  else {
            $result = parent::getLabel($labelName);
        }

        return $result;
    }
}
