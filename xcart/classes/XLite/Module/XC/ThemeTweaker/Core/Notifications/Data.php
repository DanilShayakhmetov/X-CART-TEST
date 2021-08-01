<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications;


use XLite\Model\Notification;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Order;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Password;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Product;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Products;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Profile;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\AccessControlCell;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Provider;

class Data
{
    /**
     * @var Notification
     */
    private $notification;

    protected $providers;

    /**
     * DataSource constructor.
     *
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return $this->notification->isEditable();
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return array_reduce($this->getProviders(), function ($carry, Provider $provider) {
            return $carry && $provider->isAvailable($this->getDirectory());
        }, true);
    }

    /**
     * @return boolean
     */
    public function isSuitable()
    {
        return !count($this->getSuitabilityErrors());
    }

    /**
     * @return array
     */
    public function getSuitabilityErrors()
    {
        return array_reduce($this->getProviders(), function ($carry, Provider $provider) {
            $name = $provider->getName($this->getDirectory());
            $errors = $provider->getSuitabilityErrors($this->getDirectory());

            return $carry + ($errors ? [$name => $errors] : []);
        }, []);
    }

    /**
     * Return unavailable providers names
     *
     * @return array
     */
    public function getUnavailableProviders()
    {
        return array_map(function (Provider $provider) {
            return $provider->getName($this->getDirectory());
        }, array_filter($this->getProviders(), function (Provider $provider) {
            return !$provider->isAvailable($this->getDirectory());
        }));
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->notification->getTemplatesDirectory();
    }

    /**
     * @return Provider[]
     */
    public function getProviders()
    {
        if (is_null($this->providers)) {
            $this->providers = array_filter($this->defineProviders(), function (Provider $provider) {
                return $provider->isApplicable($this->getDirectory());
            });
        }

        return $this->providers;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array_combine(
            array_map(function (Provider $provider) {
                return $provider->getName($this->getDirectory());
            }, $this->getProviders()),
            array_map(function (Provider $provider) {
                return $provider->getData($this->getDirectory());
            }, $this->getProviders())
        );
    }

    /**
     * @param array $data
     *
     * @return array Errors
     */
    public function update(array $data)
    {
        $providers = $this->getProviders();

        $errors = array_map(function (Provider $provider) use ($data) {
            if (isset($data[$provider->getName($this->getDirectory())])) {
                $entry = $data[$provider->getName($this->getDirectory())];
                if ($errors = $provider->validate($this->getDirectory(), $entry)) {
                    return $errors;
                } else {
                    $provider->setValue($this->getDirectory(), $entry);
                }
            }

            return [];
        }, $providers);

        return array_filter(array_combine(
            array_map(function (Provider $provider) {
                return $provider->getName($this->getDirectory());
            }, $providers),
            $errors
        ), function (array $errors) {
            return !empty($errors);
        });
    }

    /**
     * @return Provider[]
     */
    protected function defineProviders()
    {
        return array_merge([
            new Order(),
            new Profile(),
            new Product(),
            new Products(),
            new AccessControlCell(),
        ], StaticProvider::getProvidersForNotification(
            $this->getDirectory()
        ));
    }
}