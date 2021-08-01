<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList;

/**
 * Membership selector
 */
class MembershipSearch extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    const TYPE_EMPTY      = 'E';
    const TYPE_MEMBERSHIP = 'M';
    const TYPE_PENDING    = 'P';

    /**
     * Memberships cache
     *
     * @var array
     */
    protected $memberships = null;

    /**
     * Get memberships
     *
     * @param string $type Type of owning of membership
     *
     * @return array
     */
    protected function getMemberships($type)
    {
        if (!isset($this->memberships)) {
            $this->memberships = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll();
        }

        $result = [];

        foreach ($this->memberships as $item) {
            $key          = $type . '_' . $item->getMembershipId();
            $result[$key] = $item->getName();
        }

        return $result;
    }

    /**
     * Returns types of owning of membership
     *
     * @return array
     */
    protected function getMembershipTypes()
    {
        return [
            static::TYPE_PENDING => static::t('Pending memberships'),
        ];
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array_merge(
            [static::TYPE_EMPTY . '_' => static::t('No memberships')],
            $this->getMemberships(static::TYPE_MEMBERSHIP)
        );

        foreach ($this->getMembershipTypes() as $type => $label) {
            $list[$type] = [
                'label'   => $label,
                'options' => $this->getMemberships($type),
            ];
        }

        ksort($list);

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list                     = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('All memberships');

        return $list;
    }

    /**
     * Check - current value is selected or not
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isOptionSelected($value)
    {
        $type = static::TYPE_MEMBERSHIP;

        $processedValues = [];
        if ($this->getValue()) {
            $processedValues = array_map(
                function ($rawValue) use ($type) {
                    return $type . '_' . $rawValue;
                },
                $this->getValue()
            );
        }

        return parent::isOptionSelected($value) || in_array($value, $processedValues);
    }
}
