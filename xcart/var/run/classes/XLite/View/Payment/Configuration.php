<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

use Includes\Utils\Module\Manager;

/**
 * Payment configuration page
 */
class Configuration extends \XLite\View\AView implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    /**
     * @var array
     */
    protected $banner;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'payment/configuration/style.less';

        return array_merge(
            $list,
            $this->getWidget([], '\XLite\View\SearchPanel\Payment\Admin\Main')->getCSSFiles(),
            $this->getWidget([], '\XLite\View\ItemsList\Model\Payment\OnlineMethods')->getCSSFiles(),
            $this->getWidget([], '\XLite\View\Pager\Admin\Model\Table')->getCSSFiles(),
            $this->getWidget([], '\XLite\View\FormField\Select\Model\CountrySelector')->getCSSFiles()
        );
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        return array_merge(
            $list,
            $this->getWidget([], '\XLite\View\SearchPanel\Payment\Admin\Main')->getJSFiles(),
            $this->getWidget([], '\XLite\View\ItemsList\Model\Payment\OnlineMethods')->getJSFiles(),
            $this->getWidget([], '\XLite\View\Pager\Admin\Model\Table')->getJSFiles(),
            $this->getWidget([], '\XLite\View\FormField\Select\Model\CountrySelector')->getJSFiles()
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment/configuration/body.twig';
    }

    // {{{ Content helpers

    /**
     * Check - has active payment modules
     *
     * @return boolean
     */
    protected function hasPaymentModules()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->hasActivePaymentModules();
    }

    /**
     * Check - has installed all-in-one and acc gateways payment modules or not
     *
     * @return boolean
     */
    protected function hasGateways()
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        ];

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * Check - has added all-in-one and cc gateways payment modules or not
     *
     * @return boolean
     */
    protected function hasAddedGateways()
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_ADDED}          = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
        ];

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * Get not added all-in-one and cc gateways payment modules count
     * @todo: remove, unused
     *
     * @return integer
     */
    protected function countNonAddedGateways()
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_ADDED}          = false;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
        ];

        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * Check - has installed alternative payment modules or not
     * @todo: remove, unused
     *
     * @return boolean
     */
    protected function hasAlternative()
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = \XLite\Model\Payment\Method::TYPE_ALTERNATIVE;

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * Check - has added alternative payment modules or not
     *
     * @return boolean
     */
    protected function hasAddedAlternative()
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_ADDED}          = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = \XLite\Model\Payment\Method::TYPE_ALTERNATIVE;

        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * Get not added all-in-one and cc gateways payment modules count
     * @todo: remove, unused
     *
     * @return integer
     */
    protected function countNonAddedAlternative()
    {
        $cnd = new \XLite\Core\CommonCell;

        $cnd->{\XLite\Model\Repo\Payment\Method::P_MODULE_ENABLED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_ADDED}          = false;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}           = \XLite\Model\Payment\Method::TYPE_ALTERNATIVE;

        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->search($cnd, true);
    }

    /**
     * @return array
     */
    protected function getBanner()
    {
        if (null === $this->banner) {
            $banners = \XLite\Core\Marketplace::getInstance()->getAllBanners();
            if (is_array($banners)) {
                foreach ($banners as $banner) {
                    if ($banner['banner_section'] === 'payment') {
                        $this->banner = $banner;

                        break;
                    }
                }
            }
        }

        return $this->banner;
    }

    /**
     * Retrieve banner specific URL
     *
     * @param array $banner Banner
     *
     * @return string
     */
    protected function getBannerURL($banner)
    {
        return !empty($banner['banner_url'])
            ? $banner['banner_url']
            : $this->getBannerModuleURL($banner);
    }

    /**
     * Retrieve banner specific URL
     *
     * @param array $banner Banner
     *
     * @return string
     */
    protected function getBannerModuleURL($banner)
    {
        list($author, $name) = explode('-', $banner['banner_module']);

        return Manager::getRegistry()->getModuleServiceURL($author, $name);
    }

    /**
     * Retrieve banner image
     *
     * @param array $banner Banner
     *
     * @return string
     */
    protected function getBannerImg($banner)
    {
        return preg_replace('/^https?:/', '', $banner['banner_img']);
    }

    // }}}

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Are you sure you want to delete the selected payment method?' => static::t('Are you sure you want to delete the selected payment method?'),
        ];
    }
}
