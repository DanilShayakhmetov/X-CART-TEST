<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\AttributeValue\Customer;

/**
 * Attribute value (Select)
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\Select implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getOptionTemplate()
    {
        return $this->useBlocksMode()
            ? $this->getDir() . '/block.twig'
            : $this->getDir() . '/option.twig';
    }

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate()
    {
        if (\XLite\Core\Layout::getInstance()->getInterface() === \XLite::CUSTOMER_INTERFACE) {
            return $this->useBlocksMode()
                ? $this->getDir() . '/blocks.twig'
                : $this->getDir() . '/selectbox.twig';
        }

        return parent::getTemplate();
    }

    /**
     * Return true if blocks mode is used, otherwise selectbox is used
     *
     * @return boolean
     */
    protected function useBlocksMode()
    {
        return $this->getAttributeDisplayMode() === \XLite\Model\Attribute::BlOCKS_MODE
            && $this->getTarget() !== 'change_attribute_values';
    }
}
