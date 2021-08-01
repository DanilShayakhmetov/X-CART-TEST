<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Shipping;

/**
 * Edit shipping method dialog widget
 */
 class EditMethod extends \XLite\View\Shipping\EditMethodAbstract implements \XLite\Base\IDecorator
{
    /**
     * Shipping method
     *
     * @var \XLite\Model\Shipping\Method
     */
    protected $method;

    /**
     * Offline help template
     *
     * @return string
     */
    protected function getOfflineHelpTemplate()
    {
        /** @var \XLite\Module\XC\FreeShipping\Model\Shipping\Method $method */
        $method = $this->getMethod();

        return ($method->getFree() || $method->isFixedFee())
            ? 'modules/XC/FreeShipping/shipping/add_method/parts/offline_help.twig'
            : 'shipping/add_method/parts/offline_help.twig';
    }

    /**
     * Returns help text
     *
     * @return string
     */
    protected function getHelpText()
    {
        $method = $this->getMethod();

        return $method->isFixedFee()
            ? static::t('Shipping freight tooltip text')
            : static::t('Free shipping tooltip text');
    }

    /**
     * Returns shipping method
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        if (null === $this->method) {
            $this->method = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find(
                \XLite\Core\Request::getInstance()->methodId
            );
        }

        return $this->method;
    }
}
