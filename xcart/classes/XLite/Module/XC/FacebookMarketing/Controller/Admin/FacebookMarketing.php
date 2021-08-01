<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Controller\Admin;

use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Core\Database;
use XLite\Core\EventTask;
use XLite\Logger;
use XLite\Module\XC\FacebookMarketing\Logic\ProductFeed\Generator as ProductFeedGenerator;

/**
 * FacebookMarketing
 */
class FacebookMarketing extends \XLite\Controller\Admin\AAdmin
{
    const FB_APP_ID = 717165612441864;
    const FB_APP_SECRET = 'f64a76387882753c3cb8bdded49ea49d';
    const FB_APP_DISPLAY = 'page';
    const FB_APP_RETURN_URL = 'https://my.x-cart.com/proxy/facebook.php';
    const FB_APP_RESPONSE_TYPE = 'code';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Facebook Ads & Instagram Ads');
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = Request::getInstance();

        if (isset($request->getData()['code'])) {
            $code  = $request->getData()['code'];
            $token = $this->getFacebookAccessToken($code);

            if (!end($token)) {
                TopMessage::addInfo('The Facebook Account was not connected due to technical reasons. Please try again later');

                $this->setReturnURL(
                    $this->buildURL('facebook_marketing')
                );
            } else {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                    'category' => 'XC\FacebookMarketing',
                    'name'     => 'token',
                    'value'    => reset($token),
                ]);

                $pixelId = $this->getPixelIdFromFacebook(reset($token));
                if (!end($pixelId)) {
                    TopMessage::addInfo('The Facebook Account was not connected due to technical reasons. Please try again later');

                    $this->setReturnURL(
                        $this->buildURL('facebook_marketing')
                    );
                } else {
                    $this->setFacebookConnected(reset($pixelId));
                    TopMessage::addInfo('Facebook Business Account has been connected successfully');

                    $this->setReturnURL(
                        $this->buildURL('facebook_marketing')
                    );
                }
            }
        }

        if ($request->error) {
            TopMessage::addError($request->error);

            $this->setReturnURL(
                $this->buildURL('facebook_marketing')
            );
        }
        if ($request->product_feed_generation_completed) {
            TopMessage::addInfo('Product feed generation has been completed successfully');

            $this->setReturnURL(
                $this->buildURL('facebook_marketing')
            );

        } elseif ($request->product_feed_generation_failed) {
            TopMessage::addError('Product feed generation has been stopped');

            $this->setReturnURL(
                $this->buildURL('facebook_marketing')
            );
        }
    }

    /**
     * Unlink and revoke FB account from X-Cart
     */
    protected function doActionRevoke()
    {
        $id = $this->getFBUserScopeId();
        if (!end($id)) {
            TopMessage::addInfo('The Facebook Account was not disconnected due to technical reasons. Please try again later.');

            $this->setReturnURL(
                $this->buildURL('facebook_marketing')
            );
        } else {
            $uninstallStatus = $this->uninstallFBE(reset($id));
            if (end($uninstallStatus)) {
                $this->setFacebookDisconnected();
                TopMessage::addInfo('Facebook Business Account has been unlinked successfully');

                $this->setReturnURL(
                    $this->buildURL('facebook_marketing')
                );
            }
        }
    }

    /**
     * Send request to FB to delete FBE extension
     *
     * @param $id
     *
     * @return array
     */
    protected function uninstallFBE($id)
    {
        $ch   = curl_init();
        $post = [
            'access_token' => \XLite\Core\Config::getInstance()->XC->FacebookMarketing->token,
        ];
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/{$id}/permissions/manage_business_extension");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        $out = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($out, true);

        if(isset($return['success'])) {
            return [$return['success'], true];
        } else {
            return [$return['error']['message'], false];
        }
    }

    /**
     * Get FB account id
     *
     * @return array
     */
    protected function getFBUserScopeId()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?fields=id&access_token=" . \XLite\Core\Config::getInstance()->XC->FacebookMarketing->token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        $out = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($out, true);

        if(isset($return['id'])) {
            return [$return['id'], true];
        } else {
            return [$return['error']['message'], false];
        }
    }

    /**
     * Create FB token from code
     *
     * @param $code
     *
     * @return array
     */
    protected function getFacebookAccessToken($code)
    {
        $redirect_uri = $this->getFacebookBusinessLoginUrl($this->getProductFeedUrl());

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v8.0/oauth/access_token?client_id=" . self::FB_APP_ID . "&client_secret=" . self::FB_APP_SECRET . "&code={$code}&redirect_uri={$redirect_uri}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        $out = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($out, true);

        if(isset($return['access_token'])) {
            return [$return['access_token'], true];
        } else {
            return [$return['error']['message'], false];
        }
    }

    /**
     * Get FB Pixel Id
     *
     * @param $token
     *
     * @return array
     */
    protected function getPixelIdFromFacebook($token)
    {
        $webAddress = $this->getWebAddress();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/fbe_business/fbe_installs?fbe_external_business_id=XCart-Business-{$webAddress}&access_token={$token}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        $out = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($out, true);

        if(isset($return['data'][0]['pixel_id'])) {
            return [$return['data'][0]['pixel_id'], true];
        } else {
            return [$return['error']['message'], false];
        }
    }

    /**
     * Get Facebook Business Login Url
     *
     * @param  $feedUrl
     *
     * @return string
     */
    public function getFacebookBusinessLoginUrl($feedUrl)
    {
        $webAddress = \XLite::getInstance()->getOptions(['host_details', 'admin_host']);

        if (!$webAddress) {
            $webAddress = \XLite\Core\Config::getInstance()->Security->admin_security
                ? 'https://' . \XLite::getInstance()->getOptions(['host_details', 'https_host'])
                : 'http://' . \XLite::getInstance()->getOptions(['host_details', 'http_host']);
        } else {
            $webAddress = 'http://' . $webAddress;
        }
        $webAddress .= \XLite::getInstance()->getOptions(['host_details', 'web_dir']);

        return 'https://my.x-cart.com/proxy/facebook.php?'
            . 'client_id=' . self::FB_APP_ID
            . '&display=' . self::FB_APP_DISPLAY
            . '&redirect_uri=' . self::FB_APP_RETURN_URL
            . '&response_type=' . self::FB_APP_RESPONSE_TYPE
            . '&address=' . $webAddress
            . '&feedUrl=' . urlencode($feedUrl)
            . '&scope=manage_business_extension,catalog_management';
    }

    /**
     * Return WebAddress url
     *
     * @return string
     */
    public function getWebAddress()
    {
        $webAddress = \XLite::getInstance()->getOptions(['host_details', 'admin_host']);
        if (!$webAddress) {
            $webAddress = \XLite\Core\Config::getInstance()->Security->admin_security
                ? \XLite::getInstance()->getOptions(['host_details', 'https_host'])
                : \XLite::getInstance()->getOptions(['host_details', 'http_host']);
        }
        $webAddress .= \XLite::getInstance()->getOptions(['host_details', 'web_dir']);

        return $webAddress;
    }

    /**
     * Check if FB account connected
     *
     * @return bool
     */
    public function isFacebookConnected()
    {
        return 'Y' === \XLite\Core\Config::getInstance()->XC->FacebookMarketing->connected;
    }

    /**
     * Check if FB Pixel Id is set
     *
     * @return bool
     */
    public function isPixelSet()
    {
        return \XLite\Core\Config::getInstance()->XC->FacebookMarketing->pixel_id;
    }

    /**
     * Set FB account to connected
     *
     * @param $pixelId
     *
     * @throws \Exception
     */
    protected function setFacebookConnected($pixelId)
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOptions([
            [
                'category' => 'XC\FacebookMarketing',
                'name'     => 'connected',
                'value'    => 'Y',
            ],
            [
                'category' => 'XC\FacebookMarketing',
                'name'     => 'pixel_id',
                'value'    => $pixelId,
            ],
        ]);
    }

    /**
     * Set FB account to disconnected
     *
     * @throws \Exception
     */
    protected function setFacebookDisconnected()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOptions([
            [
                'category' => 'XC\FacebookMarketing',
                'name'     => 'connected',
                'value'    => 'N',
            ],
            [
                'category' => 'XC\FacebookMarketing',
                'name'     => 'token',
                'value'    => '',
            ],
            [
                'category' => 'XC\FacebookMarketing',
                'name'     => 'pixel_id',
                'value'    => '',
            ],
        ]);
    }

    /**
     * Check - generation process is not-finished or not
     *
     * @return boolean
     */
    public function isProductFeedGenerationNotFinished()
    {
        $eventName = ProductFeedGenerator::getEventName();
        $state = Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
               && in_array(
                   $state['state'],
                   [EventTask::STATE_STANDBY, EventTask::STATE_IN_PROGRESS]
               )
               && !Database::getRepo('XLite\Model\TmpVar')->getVar(ProductFeedGenerator::getCancelFlagVarName());
    }

    /**
     * Check - generation process is not-finished or not
     *
     * @return boolean
     */
    public function isProductFeedGenerationFinished()
    {
        return !$this->isProductFeedGenerationNotFinished();
    }

    /**
     * Check - generation process is not-finished or not
     *
     * @return boolean
     */
    public function isProductFeedGenerationAvailable()
    {
        return $this->isProductFeedGenerationFinished() && $this->getFeedProductCount() > 0;
    }

    /**
     * Count products for feed
     *
     * @return integer
     */
    public function getFeedProductCount()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->countForFacebookProductFeed();
    }

    /**
     * Manually generate sitemap
     *
     * @return void
     */
    protected function doActionGenerate()
    {
        if ($this->isProductFeedGenerationAvailable()) {
            ProductFeedGenerator::run([]);
        } elseif (!$this->getFeedProductCount()) {
            \XLite\Core\TopMessage::addWarning('There is no products for facebook product feed generation');
        }

        $this->setReturnURL(
            $this->buildURL('facebook_marketing')
        );
    }

    /**
     * Get Product Feed Url
     *
     * @return string
     */
    protected function getProductFeedUrl()
    {
        return \XLite\Core\Converter::buildFullURL('facebook_product_feed', '', [
            'key' => \XLite\Core\Config::getInstance()->XC->FacebookMarketing->product_feed_key,
        ], \XLite::CART_SELF);
    }

    /**
     * Send Product Feed to FB
     */
    protected function doActionSendFeedToFB()
    {
        $webAddress = $this->getWebAddress();

        $data = [
            'fbe_external_business_id' => 'XCart-Business-' . $webAddress,
            'access_token'             => \XLite\Core\Config::getInstance()->XC->FacebookMarketing->token,
            'url'                      => $this->getProductFeedUrl(),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/fbe_catalog_feed/uploads");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $out = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($out, true);
        if (isset($return['id'])) {
            TopMessage::addInfo('Product feed successfully sent to Facebook');

            $this->setReturnURL(
                $this->buildURL('facebook_marketing')
            );
        } else {
            TopMessage::addInfo('The product feed was not uploaded due to technical reasons. Please try again later or set up a schedule for the product feed updating in your Facebook account.');

            $this->setReturnURL(
                $this->buildURL('facebook_marketing')
            );
        }
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionProductFeedGenerationCancel()
    {
        ProductFeedGenerator::cancel();
        TopMessage::addWarning('Product feed generation has been stopped');

        $this->setReturnURL(
            $this->buildURL('facebook_marketing')
        );
    }

    /**
     * Update Facebook Marketing settings
     */
    protected function doActionUpdateSettings()
    {
        if (isset(\XLite\Core\Request::getInstance()->pixel_id)) {
            $pixelId = \XLite\Core\Request::getInstance()->pixel_id;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\FacebookMarketing',
                'name'     => 'pixel_id',
                'value'    => $pixelId,
            ]);

            \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
        }

        if (isset(\XLite\Core\Request::getInstance()->add_to_cart_value)) {
            $addToCartValue = \XLite\Core\Request::getInstance()->add_to_cart_value;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\FacebookMarketing',
                'name'     => 'add_to_cart_value',
                'value'    => $addToCartValue,
            ]);

            \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
        }

        if (isset(\XLite\Core\Request::getInstance()->view_content_value)) {
            $viewContentValue = \XLite\Core\Request::getInstance()->view_content_value;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\FacebookMarketing',
                'name'     => 'view_content_value',
                'value'    => $viewContentValue,
            ]);

            \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
        }

        if (isset(\XLite\Core\Request::getInstance()->init_checkout_value)) {
            $initCheckoutValue = \XLite\Core\Request::getInstance()->init_checkout_value;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\FacebookMarketing',
                'name'     => 'init_checkout_value',
                'value'    => $initCheckoutValue,
            ]);

            \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
        }

        if (isset(\XLite\Core\Request::getInstance()->advanced_matching)) {
            $advancedMatching = \XLite\Core\Request::getInstance()->advanced_matching;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\FacebookMarketing',
                'name'     => 'advanced_matching',
                'value'    => $advancedMatching,
            ]);

            \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
        }

        if (isset(\XLite\Core\Request::getInstance()->include_out_of_stock)) {
            $includeOutOfStock = \XLite\Core\Request::getInstance()->include_out_of_stock;
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\FacebookMarketing',
                'name'     => 'include_out_of_stock',
                'value'    => $includeOutOfStock == 1 ? 'Y' : 'N',
            ]);

            \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
        }

        if ($renewalFrequency = \XLite\Core\Request::getInstance()->renewal_frequency) {
            \XLite\Module\XC\FacebookMarketing\Core\Task\GenerateProductFeed::setRenewalPeriod($renewalFrequency);

            \XLite\Core\TopMessage::addInfo('Data have been saved successfully');
        }
    }
}