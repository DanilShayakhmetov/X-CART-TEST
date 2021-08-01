<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Marketplace;

class Constant
{
    public const REQUEST_ADDON_HASH        = 'AddonHash';
    public const REQUEST_ADDON_INFO        = 'AddonInfo';
    public const REQUEST_ADDON_PACK        = 'AddonPack';
    public const REQUEST_ADDONS            = 'Addons';
    public const REQUEST_BANNERS           = 'Banners';
    public const REQUEST_CHECK_ADDON_KEY   = 'CheckAddonKey';
    public const REQUEST_CORE_HASH         = 'CoreHash';
    public const REQUEST_CORE_PACK         = 'CorePack';
    public const REQUEST_CORES             = 'Cores';
    public const REQUEST_LANDING           = 'Landing';
    public const REQUEST_NOTIFICATIONS     = 'Notifications';
    public const REQUEST_OUTDATED_MODULE   = 'OutdatedModule';
    public const REQUEST_PAYMENT_METHODS   = 'PaymentMethods';
    public const REQUEST_GDPR_MODULES      = 'GDPRModules';
    public const REQUEST_RESEND_KEY        = 'ResendKey';
    public const REQUEST_SET               = 'Set';
    public const REQUEST_SET_KEY_WAVE      = 'SetKeyWave';
    public const REQUEST_SHIPPING_METHODS  = 'ShippingMethods';
    public const REQUEST_TAGS              = 'Tags';
    public const REQUEST_TEST              = 'Test';
    public const REQUEST_UPDATES           = 'Updates';
    public const REQUEST_WAVES             = 'Waves';
    public const REQUEST_INSTALLATION_DATA = 'InstallationData';
    public const REQUEST_CORE_LICENSE      = 'CoreLicense';

    /**
     * Request/response fields
     */
    public const FIELD_NAME                        = 'name';
    public const FIELD_IS_UPGRADE_AVAILABLE        = 'isUpgradeAvailable';
    public const FIELD_ARE_UPDATES_AVAILABLE       = 'areUpdatesAvailable';
    public const FIELD_IS_CONFIRMED                = 'isConfirmed';
    public const FIELD_BANNER_MODULE               = 'banner_module';
    public const FIELD_BANNER_IMG                  = 'banner_img';
    public const FIELD_BANNER_URL                  = 'banner_url';
    public const FIELD_BANNER_SECTION              = 'banner_section';

    public const FIELD_NOTIFICATION_TYPE        = 'type';
    public const FIELD_NOTIFICATION_MODULE      = 'module';
    public const FIELD_NOTIFICATION_IMAGE       = 'image';
    public const FIELD_NOTIFICATION_TITLE       = 'title';
    public const FIELD_NOTIFICATION_DESCRIPTION = 'description';
    public const FIELD_NOTIFICATION_LINK        = 'link';
    public const FIELD_NOTIFICATION_DATE        = 'date';
    public const FIELD_NOTIFICATION_PAGE_PARAMS = 'pageParams';
    public const FIELD_NOTIFICATION_PARAM_KEY   = 'paramKey';
    public const FIELD_NOTIFICATION_PARAM_VALUE = 'paramValue';

    public const INACTIVE_KEYS = 'inactiveMPKeys';
}
