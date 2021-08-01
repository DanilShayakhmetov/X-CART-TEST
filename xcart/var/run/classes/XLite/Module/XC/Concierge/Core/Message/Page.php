<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Message;

use XLite\Module\XC\Concierge\Core\AMessage;

class Page extends AMessage
{
    /**
     * @var string
     */
    protected $category;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param string $category
     * @param string $title
     */
    public function __construct($category, $title)
    {
        $this->category = $category;
        $this->title    = $title;
    }

    public function getType()
    {
        return static::TYPE_PAGE;
    }

    public function getArguments()
    {
        $result = [];

        // The category of the page
        //$category = $this->getCategory();
        //if ($category) {
        //    $result[] = $category;
        //}

        // The name of the page.
        // @tricky: like on x-cart.com
        $result[] = 'Loaded a page';

        // A dictionary of properties of the page. Note: url, title, referrer and path are collected automatically!
        $result[] = $this->getProperties();

        // A dictionary of options.
        $result[] = $this->getOptions();

        return $result;
    }

    /**
     * @return array
     */
    protected function getProperties()
    {
        $title = $this->getTitle();

        $admin = \XLite\Core\Auth::getInstance()->getProfile();
        $admin_email = $admin ? $admin->getLogin() : '';

        // @tricky: like on x-cart.com
        return [
            'title'       => $title,
            'Page Name'   => 'Concierge: ' . $title,
            'host'        => $_SERVER['HTTP_HOST'],
            'EventSource' => 'Concierge',
            'admin_email' => $admin_email,
            'email'       => \XLite\Core\Config::getInstance()->XC->Concierge->user_id,
            'subscriptionType' => $this->getSubscriptionType(),
            'Store License Type' => $this->getLicenseType(),
            'Store Version' => \XLite::getInstance()->getVersion(),
            'Store Admin Panel URL' => \XLite::getInstance()->getShopURL(),
        ];
    }

    /**
     * @param string $integration
     *
     * @return array
     */
    public function toArray($integration = '')
    {
        $result = parent::toArray($integration);
        if ($integration === 'intercom') {
            $result['arguments'][0] .= ' ' . $this->getTitle();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
