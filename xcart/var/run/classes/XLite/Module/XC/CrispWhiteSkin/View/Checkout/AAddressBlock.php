<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Checkout;

/**
 * Address block info
 */
abstract class AAddressBlock extends \XLite\View\Checkout\AAddressBlockAbstract implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'checkout/fields-height-controller.js'
            ]
        );
    }

    /**
     * Get field placeholder
     *
     * @param string $name File short name
     *
     * @return string
     */
    protected function getFieldPlaceholder($name)
    {
        switch ($name) {
            case 'firstname':
                $result = static::t('Joe');
                break;

            case 'lastname':
                $result = static::t('Public');
                break;

            case 'street':
                $result = static::t('1000 Main Street');
                break;

            case 'city':
                $result = static::t('Anytown');
                break;

            case 'custom_state':
                $result = static::t('Anyland');
                break;

            case 'zipcode':
                $result = static::t('90001');
                break;

            case 'phone':
                $result = static::t('+15550000000');
                break;

            case 'email':
                $result = static::t('email@example.com');
                break;

            default:
                $result = '';
        }

        return $result;
    }
}
