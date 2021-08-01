<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Controller\Customer;


/**
 * Class ACustomer
 */
 class ACustomer extends \XLite\Module\CDev\Paypal\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    protected function getCartFingerprintDifference(array $old, array $new)
    {
        $result = parent::getCartFingerprintDifference($old, $new);

        $cellKeys = array(
            'shippingMethodName',
            'paymentMethodName',
        );

        foreach ($cellKeys as $name) {
            $old[$name] = isset($old[$name]) ? $old[$name] : '';
            $new[$name] = isset($new[$name]) ? $new[$name] : '';

            if ($old[$name] != $new[$name]) {
                $result[$name] = $new[$name];
            }
        }

        return $result;
    }

    /**
     * @return mixed|null
     */
    protected function parseClientIdCookie()
    {
        $cid = null;

        if (isset($_COOKIE['_ga'])) {
            @list($version,$domainDepth, $cid1, $cid2) = explode('.', $_COOKIE["_ga"], 4);
            $contents = [
                'version'       => $version,
                'domainDepth'   => $domainDepth,
                'cid'           => $cid1.'.'.$cid2,
            ];

            $cid = $contents['cid'];
        }

        return $cid;
    }

    /**
     * @inheritDoc
     */
    protected function updateCart($silent = false)
    {
        parent::updateCart($silent);

        /** @var \XLite\Module\CDev\GoogleAnalytics\Model\Profile $profile */
        $profile = $this->getCart()->getProfile();

        if ($profile && $this->parseClientIdCookie()) {
            $profile->setGaClientId($this->parseClientIdCookie());
            $profile->update();
        }
    }

    /**
     * @param $category
     *
     * @return string
     */
    public function getGACategoryPath($category)
    {
        return $this->executeCachedRuntime(function () use ($category) {
            $categoryPath = $category->getPath();

            if (count($categoryPath) > 5) {
                $categoryPath = array_merge(array_slice($categoryPath, 0, 4), end($categoryPath));
            }

            $categoryName = implode(
                '/',
                array_map(
                    function ($elem) {
                        return $elem->getName();
                    },
                    $categoryPath
                )
            );

            return $categoryName;
        }, ['getGACategoryPath', $category->getCategoryId()]);
    }
}