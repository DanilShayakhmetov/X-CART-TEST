<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormField\Inline\Input\Hidden;

/**
 * Hidden order modifier total
 */
class OrderModifierTotal extends \XLite\View\FormField\Inline\Base\Single
{

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Hidden';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-hidden';
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/input/text.view.twig';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function isEditable()
    {
        return true;
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * Get field name parts
     *
     * @param array $field Field
     *
     * @return array
     */
    protected function getNameParts(array $field)
    {
        return array(
            'orderModifiers',
            $this->getParam(static::PARAM_FIELD_NAME),
        );
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if ($this->getParam(static::PARAM_ENTITY) && !$this->getParam(static::PARAM_FIELD_NAME)) {
            $this->getWidgetParams(static::PARAM_FIELD_NAME)->setValue(
                $this->getParam(static::PARAM_ENTITY)->getCode()
            );
        }
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        $value = $this->getEntity()->getValue();
        if ($this->getEntity()->getType() === \XLite\Model\Base\Surcharge::TYPE_DISCOUNT && $value < 0) {
            $value = abs($value);
        }

        return $value;
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $oldValue = $this->getEntity()->getOrder()->getCurrency()->roundValue($this->getEntity()->getValue());

        $this->updateCouponSurcharge($this->getEntity());

        $newValue = $this->getEntity()->getOrder()->getCurrency()->roundValue($this->getEntity()->getValue());

        if ($oldValue !== $newValue) {
            \XLite\Controller\Admin\Order::setOrderChanges(
                $this->getParam(static::PARAM_FIELD_NAME),
                static::formatPrice(abs($newValue), $this->getEntity()->getOrder()->getCurrency(), true),
                static::formatPrice(abs($oldValue), $this->getEntity()->getOrder()->getCurrency(), true)
            );
        }
    }

    /**
     * @param \XLite\Model\Order\Surcharge $couponSurcharge
     * @throws \Doctrine\ORM\ORMException
     */
    protected function updateCouponSurcharge(\XLite\Model\Order\Surcharge $couponSurcharge)
    {
        $isPersistent = $couponSurcharge->isPersistent();
        $value = 0;

        if ($couponSurcharge->getModifier()) {
            $surcharges = $couponSurcharge->getModifier()->calculate();

            if (!is_array($surcharges)) {
                $surcharges = [$surcharges];
            }

            foreach ($surcharges as $surcharge) {
                if (is_object($surcharge)) {
                    $value += $surcharge->getValue();

                    if ($isPersistent) {
                        \XLite\Core\Database::getEM()->remove($surcharge);
                        $surcharge->getOrder()->removeSurcharge($surcharge);
                    }
                }
            }
        }

        if (0 < $value && $couponSurcharge->getType() === \XLite\Model\Base\Surcharge::TYPE_DISCOUNT) {
            $value = $value * -1;
        }

        $couponSurcharge->setValue($value);
    }
}
