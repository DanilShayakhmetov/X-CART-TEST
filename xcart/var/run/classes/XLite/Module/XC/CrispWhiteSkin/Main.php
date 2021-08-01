<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin;

use Includes\Utils\Module\Manager;
use XLite\Core\Layout;

abstract class Main extends \XLite\Module\AModuleSkin
{
    /**
     * Check if skin is based on Crisp White theme
     *
     * @return boolean
     */
    public static function isCrispWhiteBasedSkin()
    {
        return true;
    }

    /**
     * Returns supported layout types
     *
     * @return array
     */
    public static function getLayoutTypes()
    {
        return [
            Layout::LAYOUT_GROUP_DEFAULT => Layout::getInstance()->getLayoutTypes(),
            Layout::LAYOUT_GROUP_HOME    => Layout::getInstance()->getLayoutTypes(),
        ];
    }

    /**
     * Returns image sizes
     *
     * @return array
     */
    public static function getImageSizes()
    {
        return [
            \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT => [
                'SBSmallThumbnail' => [120, 120],
                'XSThumbnail'      => [60, 60],
                'MSThumbnail'      => [60, 60],
            ],
        ];
    }

    /**
     * Determines if some module is enabled
     *
     * @return boolean
     */
    public static function isModuleEnabled($name)
    {
        list($author, $name) = explode('\\', $name);

        return Manager::getRegistry()->isModuleEnabled($author, $name);
    }

    /**
     * Check if skin supports cloud zoom
     *
     * @return boolean
     */
    public static function isUseCloudZoom()
    {
        return true;
    }

    /**
     * Check if image lazy loading is supported by skin
     *
     * @return boolean
     */
    public static function isUseLazyLoad()
    {
        return true;
    }

    protected static function moveTemplatesInLists()
    {
        $templates = [
            'authorization/parts/field.links.twig'                          => [
                static::TO_ADD => [
                    ['customer.signin.popup.fields', 500, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/content/main.location.twig'                             => [
                static::TO_DELETE => [
                    ['layout.main', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['center.top', 1000, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/content/product.location.twig'                          => [
                static::TO_ADD => [
                    ['product.details.page.info', 5, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/top_menu.twig'                                   => [
                static::TO_DELETE => [
                    ['layout.main', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['layout.header', 300, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/header.right.twig'                               => [
                static::TO_DELETE => [
                    ['layout.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['layout.header', 350, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.search.twig'                          => [
                static::TO_DELETE => [
                    ['layout.header.bar', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['layout.header.bar', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/header.bar.checkout.logos.twig'                  => [
                static::TO_DELETE => [],
                static::TO_ADD    => [
                    ['layout.header.right.mobile', 1100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/account_menu.twig'           => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/language_menu.twig'          => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/search_menu.twig'            => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'layout/header/mobile_header_parts/slidebar_menu.twig'          => [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['layout.header.mobile.menu', 2000, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'mini_cart/horizontal/parts/mobile.icon.twig'                   => [
                static::TO_DELETE => [
                    ['minicart.horizontal.children', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.labels.twig'                   => [
                static::TO_DELETE => [
                    ['itemsList.product.small_thumbnails.customer.details', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['itemsList.product.small_thumbnails.customer.info.photo', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.image', 17, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'shopping_cart/parts/item.remove.twig'                          => [
                static::TO_DELETE => [
                    ['cart.item', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['cart.item', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'shopping_cart/parts/item.info.weight.twig'                     => [
                static::TO_DELETE => [
                    ['cart.item.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['cart.item.info', 15, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/stock/label.twig'                              => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['itemsList.product.grid.customer.info', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.info', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'items_list/product/parts/common.sort-options.twig'             => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['itemsList.product.grid.customer.header', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.display-modes.twig'            => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['itemsList.product.grid.customer.header', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.header', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.header', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'items_list/product/parts/common.product-name.twig'             => [
                static::TO_DELETE => [
                    ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.mainBlock', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'items_list/product/center/list/parts/common.product-name.twig' => [
                static::TO_ADD => [
                    ['itemsList.product.list.customer.info', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.field-select-product.twig'     => [
                static::TO_DELETE => [
                    ['itemsList.product.table.customer.columns', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/common.field-product-qty.twig'        => [
                static::TO_DELETE => [
                    ['itemsList.product.table.customer.columns', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'items_list/product/parts/table.captions.field-select-all.twig' => [
                static::TO_DELETE => [
                    ['itemsList.product.table.customer.captions', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'items_list/product/parts/grid.photo.twig'        => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.mainBlock', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'items_list/product/parts/common.product-thumbnail.twig'   => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info.photo', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD => [
                    ['itemsList.product.grid.customer.mainBlock.photo', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'items_list/product/parts/common.added-mark.twig'               => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.photo', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.small_thumbnails.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.big_thumbnails.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['itemsList.product.grid.customer.marks', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.marks', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.marks', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'product/details/parts/common.image-next.twig'                  => [
                static::TO_DELETE => [
                    ['product.details.page.image.photo', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'product/details/parts/common.image-previous.twig'              => [
                static::TO_DELETE => [
                    ['product.details.page.image.photo', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'product/details/parts/common.loupe.twig'                       => [
                static::TO_DELETE => [
                    ['product.details.page.image', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['product.details.page.image.photo', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'product/details/parts/common.briefDescription.twig'            => [
                static::TO_DELETE => [
                    ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['product.details.page.info', 19, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            
            'product/details/parts/common.product-editable-attributes.twig' => [
                static::TO_DELETE => [
                    ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['product.details.page.info', 35, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'product/details/parts/common.more-info-link.twig'              => [
                static::TO_DELETE => [
                    ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['product.details.quicklook.image', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'items_list/product/parts/grid.button-add2cart-wrapper.twig'    => [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.tail', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['itemsList.product.grid.customer.buttons', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'product/details/parts/common.product-added.twig'               => [
                static::TO_DELETE => [
                    ['product.details.page.info.form.buttons-added', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info.form.buttons-added', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'product/details/parts/common.product-title.twig'               => [
                static::TO_DELETE => [
                    ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],

            'checkout/steps/shipping/parts/address.billing.same.twig' => [
                static::TO_DELETE => [
                    ['checkout.payment.address.after', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['checkout.payment.address.before', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
        ];

        if (static::isModuleEnabled('CDev\GoSocial')) {
            $templates += [
                'modules/CDev/GoSocial/product/details/parts/common.share.twig' => [
                    static::TO_DELETE => [
                        ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD    => [
                        ['product.details.page.image', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('CDev\ProductAdvisor')) {
            $templates += [
                'modules/CDev/ProductAdvisor/product/details/parts/common.coming_soon.twig' => [
                    static::TO_DELETE => [
                        ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD    => [
                        ['product.details.page.info', 16, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['product.details.quicklook.info', 16, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/CDev/ProductAdvisor/expected.twig'                                 => [
                    static::TO_DELETE => [
                        ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD    => [
                        ['itemsList.product.grid.customer.info', 27, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['itemsList.product.list.customer.info', 33, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\FastLaneCheckout')) {
            $templates += [
                'modules/XC/FastLaneCheckout/checkout_fastlane/header/back_button.twig' => [
                    static::TO_DELETE => [
                        ['checkout_fastlane.header.left', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\Reviews')) {
            $templates += [
                'modules/XC/Reviews/product.items_list.rating.twig'              => [
                    static::TO_DELETE => [
                        ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['itemsList.product.list.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD    => [
                        ['itemsList.product.grid.customer.info', 26, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['itemsList.product.list.customer.info', 35, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/product_details.rating.twig'                 => [
                    static::TO_DELETE => [
                        ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                    static::TO_ADD    => [
                        ['product.details.quicklook.image', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                        ['product.details.page.info', 18, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/reviews_page/parts/average_rating.form.twig' => [
                    static::TO_ADD => [
                        ['reviews.page.rating', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
                'modules/XC/Reviews/average_rating/form.twig'                    => [
                    static::TO_DELETE => [
                        ['reviews.page.rating', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ],
                ],
            ];
        }

        if (static::isModuleEnabled('CDev\SocialLogin')) {
            $templates['modules/CDev/SocialLogin/signin/signin.checkout.social.twig'] = [
                static::TO_DELETE => [
                    ['customer.checkout.signin', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['signin.main', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('Amazon\PayWithAmazon')) {
            $templates['modules/Amazon/PayWithAmazon/login/signin/signin.checkout.twig'] = [
                static::TO_DELETE => [
                    ['customer.checkout.signin', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['signin.main', 30, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('CDev\Paypal')) {
            $templates['modules/CDev/Paypal/login/signin/signin.checkout.paypal.twig'] = [
                static::TO_DELETE => [
                    ['customer.checkout.signin', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['signin.main', 40, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\ShopperApproved')) {
            $templates['modules/XC/ShopperApproved/average_rating/details.twig'] = [
                static::TO_DELETE => [
                    ['product.details.quicklook.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['product.details.quicklook.image', 29, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\ProductComparison') || \XLite\Core\Config::getInstance()->General->enable_add2cart_button_grid){
            $templates['items_list/product/parts/grid.buttons-container.twig'] = [
                static::TO_ADD    => [
                    ['itemsList.product.grid.customer.info', 1100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        return $templates;
    }

    /**
     * @return array
     */
    protected static function moveClassesInLists()
    {
        $classes_list = [
            'XLite\View\MinicartAttributeValues'           => [
                ['minicart.horizontal.item', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ['minicart.horizontal.item.name', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
            ],
            'XLite\View\Product\Details\Customer\PhotoBox' => [
                ['product.details.page.image', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ['product.details.page.image', 5, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
            ],

            'XLite\View\LanguageSelector\Customer'             => [
                static::TO_DELETE => [
                    ['layout.header.bar.links.newby', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['layout.header.bar.links.logged', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\TopContinueShopping'                   => [
                static::TO_DELETE => [
                    ['layout.main.breadcrumb', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\Product\Details\Customer\Gallery'      => [
                static::TO_DELETE => [
                    ['product.details.page.image', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['product.details.page', 15, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook', 15, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\ShippingEstimator\ShippingEstimateBox' => [
                static::TO_DELETE => [
                    ['cart.panel.box', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
            'XLite\View\BannerRotation\BannerRotation'         => [
                static::TO_DELETE => [
                    ['center', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['layout.main', 350, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
        ];

        if (static::isModuleEnabled('CDev\Coupons')) {
            $classes_list['XLite\Module\CDev\Coupons\View\CartCoupons'] = [
                static::TO_DELETE => [
                    ['checkout.review.selected', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['checkout.review.selected', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\GiftWrapping')) {
            $classes_list['XLite\Module\XC\GiftWrapping\View\GiftWrapping'] = [
                static::TO_DELETE => [
                    ['checkout.review.selected', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['checkout.review.selected', 300, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\Geolocation')) {
            $classes_list['XLite\Module\XC\Geolocation\View\Button\LocationSelectPopup'] = [
                ['layout.header.bar', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
            ];
        }

        if (static::isModuleEnabled('XC\MultiCurrency')) {
            $classes_list['XLite\Module\XC\MultiCurrency\View\LanguageSelector\CustomerMobile'] = [
                static::TO_DELETE => [
                    ['layout.header.mobile.menu', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['slidebar.settings', 0, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\NextPreviousProduct')) {
            $classes_list['XLite\Module\XC\NextPreviousProduct\View\Product\Details\Customer\NextPreviousProduct'] = [
                static::TO_DELETE => [
                    ['product.details.page.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['product.details.page', 17, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\NewsletterSubscriptions')) {
            $classes_list['XLite\Module\XC\NewsletterSubscriptions\View\SubscribeBlock'] = [
                static::TO_DELETE => [
                    ['layout.main.footer', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['layout.main.footer.before', 10, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        if (static::isModuleEnabled('XC\ProductComparison')) {
            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\Product'] = [
                static::TO_ADD => [
                    ['product.details.quicklook.info.form.buttons.cart-buttons', 120, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['product.details.quicklook.info.form.buttons-added.cart-buttons', 129, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\ProductCompareIndicator'] = [
                static::TO_ADD => [
                    ['layout.header.right', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['layout.header.right.mobile', 50, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\ProductCompareLink'] = [
                static::TO_ADD => [
                    ['slidebar.additional-menu.links', 20, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\AddToCompare\Products'] = [
                static::TO_DELETE => [
                    ['itemsList.product.grid.customer.info', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
                static::TO_ADD    => [
                    ['itemsList.product.grid.customer.buttons', 200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.list.customer.info', 1200, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['itemsList.product.table.customer.columns', 47, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];

            $classes_list['XLite\Module\XC\ProductComparison\View\ProductComparison'] = [
                static::TO_DELETE => [
                    ['sidebar.single', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                    ['sidebar.second', \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ];
        }

        return $classes_list;
    }
}
