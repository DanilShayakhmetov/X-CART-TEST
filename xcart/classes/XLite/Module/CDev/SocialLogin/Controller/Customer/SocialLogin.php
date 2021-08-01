<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Controller\Customer;

use XLite\Module\CDev\SocialLogin\Core\AAuthProvider;

/**
 * Authorization grants are routed to this controller
 */
class SocialLogin extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Perform login action
     *
     * @return void
     */
    protected function doActionLogin()
    {
        $authProviders = \XLite\Module\CDev\SocialLogin\Core\AuthManager::getAuthProviders();

        $requestProcessed = false;

        foreach ($authProviders as $authProvider) {
            /** @var AAuthProvider $authProvider */
            if (!$authProvider->detectAuth()) {
                continue;
            }

            $profileInfo = $authProvider->processAuth();

            if ($profileInfo && !empty($profileInfo['id'])) {

                if (!empty($profileInfo['email'])) {
                    $this->processProfileInfo($authProvider, $profileInfo);

                } else {
                    \XLite\Core\TopMessage::addWarning(
                        'Profile does not have any email address. Please sign in the classic way.'
                    );
                    $this->setAuthReturnUrl($authProvider::STATE_PARAM_NAME, true);
                }

                $requestProcessed = true;
            }
        }

        if (!$requestProcessed) {
            \XLite\Core\TopMessage::addError('We were unable to process this request');
            $this->setAuthReturnUrl('', true);
        }
    }

    /**
     * @param AAuthProvider $authProvider
     * @param array         $profileInfo
     */
    protected function processProfileInfo(AAuthProvider $authProvider, array $profileInfo)
    {
        $profile = $this->getSocialLoginProfile(
            $profileInfo['email'],
            $authProvider->getName(),
            $profileInfo['id']
        );

        if ($profile) {
            if (!$profile->getFirstAddress()) {
                $address = $authProvider->processAddress($profileInfo);
                if ($address) {
                    $address->setProfile($profile);
                    \XLite\Core\Database::getEM()->persist($address);

                    $profile->addAddresses($address);
                }
            }

            $picture = $authProvider->processPicture($profileInfo);

            if ($picture && !$profile->getPictureUrl()) {
                $profile->setPictureUrl($picture);
            }

            if ($profile->isEnabled()) {
                \XLite\Core\Auth::getInstance()->loginProfile($profile);

                // We merge the logged in cart into the session cart
                /** @var \XLite\Model\Cart $profileCart */
                $profileCart = $this->getCart();
                $profileCart->login($profile);
                \XLite\Core\Database::getEM()->flush();

                if ($profileCart->isPersistent()) {
                    $this->updateCart();
                }

                $this->setAuthReturnUrl($authProvider::STATE_PARAM_NAME);

            } else {
                \XLite\Core\TopMessage::addError('Profile is disabled');
                $this->setAuthReturnUrl($authProvider::STATE_PARAM_NAME, true);
            }

        } else {
            $provider = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->findOneBy(['login' => $profileInfo['email'], 'order' => null])
                ->getSocialLoginProvider();

            $labelArgs = [];
            if ($provider) {
                $signInVia = 'The email you tried to use is already registered in our store. Please try logging in using your X account.';
                $labelArgs = ['provider' => ucfirst($provider)];
            } else {
                $signInVia = 'The email you tried to use is already registered in our store. Please sign in the classic way.';
            }

            \XLite\Core\TopMessage::addWarning($signInVia, $labelArgs);
            $this->setAuthReturnUrl($authProvider::STATE_PARAM_NAME, true);
        }
    }
    /**
     * Fetches an existing social login profile or creates new
     *
     * @param string $login          E-mail address
     * @param string $socialProvider SocialLogin auth provider
     * @param string $socialId       SocialLogin provider-unique id
     *
     * @return \XLite\Model\Profile
     */
    protected function getSocialLoginProfile($login, $socialProvider, $socialId)
    {
        $profile = \XLite\Core\Database::getRepo('\XLite\Model\Profile')->findOneBy(
            [
                'socialLoginProvider' => $socialProvider,
                'socialLoginId'       => $socialId,
                'order'               => null,
            ]
        );

        if (!$profile) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->findOneBy(['login' => $login, 'order' => null, 'anonymous' => false]);

            if (!$profile) {
                $profile = new \XLite\Model\Profile();
                $profile->setLogin($login);
                if ($profile->create()) {
                    \XLite\Core\Mailer::sendProfileCreated($profile);
                }

            } else {
                $profile = null;
            }
        }

        if ($profile) {
            $profile->setSocialLoginProvider($socialProvider);
            $profile->setSocialLoginId($socialId);
        }

        return $profile;
    }

    /**
     * Set redirect URL
     *
     * @param string $stateParamName Name of the state parameter containing
     *                               class name of the controller that initialized auth request OPTIONAL
     * @param mixed  $failure        Indicates if auth process failed OPTIONAL
     *
     * @return void
     */
    protected function setAuthReturnUrl($stateParamName = '', $failure = false)
    {
        $state = \XLite\Core\Request::getInstance()->$stateParamName;
        $state = $state
            ? explode(\XLite\Module\CDev\SocialLogin\Core\AAuthProvider::STATE_DELIMITER, urldecode($state))
            : [];
        $controller = $state[0] ?? null;
        $returnURL = $state[1] ?? null;

        if ($failure) {
            $this->setReturnURL($returnURL);
            return;
        }
        
        $redirectTo = '';

        if ('XLite\Controller\Customer\Checkout' === $controller) {
            $redirectTo = 'checkout';
        } elseif ('XLite\Controller\Customer\Profile' === $controller) {
            $redirectTo = 'profile';
        }

        if (empty($redirectTo) && $returnURL && $this->checkReturnUrl($returnURL)) {
            if (strpos($returnURL, \XLite\Core\Converter::buildURL('login')) !== false) {
                $returnURL = \XLite\Core\Converter::buildFullURL('main');
            }

            $this->setReturnURL(\Includes\Utils\URLManager::getShopURL($returnURL));
        } else {
            $this->setReturnURL($this->buildFullURL(urldecode($redirectTo)));
        }
    }

    /**
     * Check if return url is relative or
     *
     * @param $url
     *
     * @return bool
     */
    protected function checkReturnUrl($url) {
        if (preg_match("#^https?://([^/]+)/#", $url, $matches)) {
            return in_array(
                $matches[1],
                $this->getStoreDomains()
            );
        }

        return !preg_match('/^https?:\/\//Ss', $url);
    }

    /**
     * Return store allowed domains
     *
     * @return array
     */
    protected function getStoreDomains()
    {
        $domains = explode(',', \XLite\Core\ConfigParser::getOptions(['host_details', 'domains']));
        $domains[] = \XLite\Core\ConfigParser::getOptions(['host_details', 'http_host']);
        $domains[] = \XLite\Core\ConfigParser::getOptions(['host_details', 'https_host']);

        return array_unique(array_filter($domains));
    }

    /**
     * Check - need to redirect
     *
     * @return boolean
     */
    public function checkLanguage()
    {
        return true;
    }
}
