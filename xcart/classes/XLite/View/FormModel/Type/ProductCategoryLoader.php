<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Loader for {@link ProductCategoryType}
 */
class ProductCategoryLoader implements ChoiceLoaderInterface
{
    use ExecuteCachedTrait;

    /**
     * @param null $value
     *
     * @return ChoiceListInterface
     */
    public function loadChoiceList($value = null)
    {
        return new ArrayChoiceList([]);
    }

    /**
     * @param array $values
     * @param null  $value
     *
     * @return array
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        return $values;
    }

    /**
     * @param array         $choices
     * @param null|callable $value
     *
     * @return string[]
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        return array_map('strval', $choices);
    }

    /**
     * @param string|int $value
     *
     * @return string
     */
    public function getValueLabel($value)
    {

        return $value;
    }
}
