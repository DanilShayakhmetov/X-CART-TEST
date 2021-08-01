<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace;

class Constant
{
    const REQUEST_ADDON_HASH        = 'AddonHash';
    const REQUEST_ADDON_HASH_BATCH  = 'AddonHashBatch';
    const REQUEST_ADDON_INFO        = 'AddonInfo';
    const REQUEST_ADDON_PACK        = 'AddonPack';
    const REQUEST_ADDONS            = 'Addons';
    const REQUEST_ADDONS_SEARCH     = 'AddonsSearch';
    const REQUEST_BANNERS           = 'Banners';
    const REQUEST_CHECK_ADDON_KEY   = 'CheckAddonKey';
    const REQUEST_CORE_HASH         = 'CoreHash';
    const REQUEST_CORE_PACK         = 'CorePack';
    const REQUEST_CORES             = 'Cores';
    const REQUEST_LANDING           = 'Landing';
    const REQUEST_NOTIFICATIONS     = 'Notifications';
    const REQUEST_OUTDATED_MODULE   = 'OutdatedModule';
    const REQUEST_PAYMENT_METHODS   = 'PaymentMethods';
    const REQUEST_GDPR_MODULES      = 'GDPRModules';
    const REQUEST_RESEND_KEY        = 'ResendKey';
    const REQUEST_SET               = 'Set';
    const REQUEST_SET_KEY_WAVE      = 'SetKeyWave';
    const REQUEST_SHIPPING_METHODS  = 'ShippingMethods';
    const REQUEST_TAGS              = 'Tags';
    const REQUEST_TEST              = 'Test';
    const REQUEST_UPDATES           = 'Updates';
    const REQUEST_WAVES             = 'Waves';
    const REQUEST_INSTALLATION_DATA = 'InstallationData';
    const REQUEST_CORE_LICENSE      = 'CoreLicense';
    const REQUEST_GET_TOKEN_DATA    = 'GetTokenData';
    //const REQUEST_VERSION_INFO      = 'VersionInfo';

    /**
     * Marketplace request types
     */
    const ACTION_CHECK_FOR_UPDATES     = 'check_for_updates'; // done
    const ACTION_GET_CORES             = 'get_cores';
    const ACTION_GET_CORE_PACK         = 'get_core_pack';
    const ACTION_GET_CORE_HASH         = 'get_core_hash';
    const ACTION_GET_ADDONS_LIST       = 'get_addons';
    const ACTION_ADDONS_SEARCH         = 'addons_search';
    const ACTION_GET_ADDON_PACK        = 'get_addon_pack';
    const ACTION_GET_ADDON_INFO        = 'get_addon_info';
    const ACTION_GET_ADDON_HASH        = 'get_addon_hash';
    const ACTION_GET_ADDON_HASH_BATCH  = 'get_addon_hash_batch';
    const ACTION_CHECK_ADDON_KEY       = 'check_addon_key';
    const ACTION_GET_HOSTING_SCORE     = 'get_hosting_score';
    const ACTION_GET_ALL_TAGS          = 'get_all_tags';
    const ACTION_GET_ALL_BANNERS       = 'get_all_banners';
    const ACTION_GET_LANDING_AVAILABLE = 'is_landing_available';
    const ACTION_TEST_MARKETPLACE      = 'test_marketplace';
    const ACTION_RESEND_KEY            = 'resend_key';
    const ACTION_REQUEST_FOR_UPGRADE   = 'request_for_upgrade';
    const ACTION_GET_XC5_NOTIFICATIONS = 'get_XC5_notifications';
    const ACTION_GET_WAVES             = 'get_waves';
    const ACTION_CHANGE_KEY_WAVE       = 'change_key_wave';
    const ACTION_UPDATE_PM             = 'update_pm';
    const ACTION_UPDATE_SHM            = 'update_shm';
    const ACTION_GET_DATASET           = 'get_dataset';
    const ACTION_GET_TOKEN_DATA        = 'get_token_data';
    const ACTION_INSTALLATION_DATA     = 'get_installation_data';
    const ACTION_CORE_LICENSE          = 'get_core_license';
    const ACTION_GET_SECTIONS          = 'get_sections';
    const ACTION_GET_EDITIONS          = 'get_editions';

    /**
     * Config fields
     */
    const CONFIG_MARKETPLACE_URL      = 'marketplace_url';
    const CONFIG_SHOP_ID              = 'shop_id';
    const CONFIG_SHOP_DOMAIN          = 'shop_domain';
    const CONFIG_SHOP_URL             = 'shop_url';
    const CONFIG_CORE_VERSION         = 'core_version';
    const CONFIG_VERSION_CORE_CURRENT = 'version_core_current';
    const CONFIG_XCN_LICENSE_KEY      = 'xcn_license_key';
    const CONFIG_INSTALLATION_LNG     = 'installation_lng';
    const CONFIG_AFFILIATE_ID         = 'affiliate_id';
    const CONFIG_LOGGER               = 'logger';
    const CONFIG_LOG_DATA             = 'log_data';
    const CONFIG_LOG_EACH_REQUEST     = 'log_each_request';

    /**
     * Request/response fields
     */
    const FIELD_VERSION_CORE_CURRENT        = 'currentCoreVersion';
    const FIELD_MODULE_VERSION              = 'currentModuleVersion';
    const FIELD_VERSION                     = 'version';
    const FIELD_VERSION_MAJOR               = 'major';
    const FIELD_VERSION_MINOR               = 'minor';
    const FIELD_VERSION_BUILD               = 'build';
    const FIELD_MIN_CORE_VERSION            = 'minorRequiredCoreVersion';
    const FIELD_REVISION                    = 'revision';
    const FIELD_REVISION_DATE               = 'revisionDate';
    const FIELD_LANDING_POSITION            = 'landingPosition';
    const FIELD_LENGTH                      = 'length';
    const FIELD_GZIPPED                     = 'gzipped';
    const FIELD_NAME                        = 'name';
    const FIELD_KEY_TYPE                    = 'keyType';
    const FIELD_MODULE                      = 'module';
    const FIELD_MODULES                     = 'modules';
    const FIELD_INFO                        = 'info';
    const FIELD_AUTHOR                      = 'author';
    const FIELD_KEY                         = 'key';
    const FIELD_KEYS                        = 'keys';
    const FIELD_WAVE                        = 'wave';
    const FIELD_EMAIL                       = 'email';
    const FIELD_INSTALLATION_LNG            = 'installation_lng';
    const FIELD_DO_REGISTER                 = 'doRegister';
    const FIELD_IS_UPGRADE_AVAILABLE        = 'isUpgradeAvailable';
    const FIELD_ARE_UPDATES_AVAILABLE       = 'areUpdatesAvailable';
    const FIELD_IS_CONFIRMED                = 'isConfirmed';
    const FIELD_READABLE_NAME               = 'readableName';
    const FIELD_READABLE_AUTHOR             = 'readableAuthor';
    const FIELD_MODULE_ID                   = 'moduleId';
    const FIELD_DESCRIPTION                 = 'description';
    const FIELD_PRICE                       = 'price';
    const FIELD_ORIG_PRICE                  = 'orig_price';
    const FIELD_CURRENCY                    = 'currency';
    const FIELD_ICON_URL                    = 'iconURL';
    const FIELD_LIST_ICON_URL               = 'listIconURL';
    const FIELD_PAGE_URL                    = 'pageURL';
    const FIELD_AUTHOR_PAGE_URL             = 'authorPageURL';
    const FIELD_DEPENDENCIES                = 'dependencies';
    const FIELD_RATING                      = 'rating';
    const FIELD_RATING_RATE                 = 'rate';
    const FIELD_RATING_VOTES_COUNT          = 'votesCount';
    const FIELD_DOWNLOADS_COUNT             = 'downloadCount';
    const FIELD_HAS_LICENSE                 = 'has_license';
    const FIELD_LICENSE                     = 'license';
    const FIELD_SHOP_ID                     = 'shopID';
    const FIELD_SHOP_DOMAIN                 = 'shopDomain';
    const FIELD_SHOP_URL                    = 'shopURL';
    const FIELD_ERROR_CODE                  = 'error';
    const FIELD_ERROR_MESSAGE               = 'message';
    const FIELD_IS_SYSTEM                   = 'isSystem';
    const FIELD_XCN_PLAN                    = 'xcn_plan';
    const FIELD_XCN_LICENSE_KEY             = 'xcn_license_key';
    const FIELD_TAGS                        = 'tags';
    const FIELD_AUTHOR_EMAIL                = 'authorEmail';
    const FIELD_IS_LANDING                  = 'isLanding';
    const FIELD_BANNER_MODULE               = 'banner_module';
    const FIELD_BANNER_IMG                  = 'banner_img';
    const FIELD_BANNER_URL                  = 'banner_url';
    const FIELD_BANNER_SECTION              = 'banner_section';
    const FIELD_EDITION_STATE               = 'edition_state';
    const FIELD_EDITIONS                    = 'editions';
    const FIELD_KEY_DATA                    = 'keyData';
    const FIELD_VERSION_API                 = 'versionAPI';
    const FIELD_LANDING                     = 'landing';
    const FIELD_XB_PRODUCT_ID               = 'xbProductId';
    const FIELD_PRIVATE                     = 'private';
    const FIELD_IS_REQUEST_FOR_UPGRADE_SENT = 'isRequestForUpgradeSent';
    const FIELD_AFFILIATE_ID                = 'affiliateId';
    const FIELD_TRIAL                       = 'trial';
    const FIELD_MODULE_ENABLED              = 'enabled';
    const FIELD_QUERIES                     = 'querySets';
    const FIELD_SALES_CHANNEL_POS           = 'salesChannelPos';
    const FIELD_TOKEN                       = 'token';
    const FIELD_PURCHASE                    = 'purchase';
    const FIELD_PROLONGATION                = 'prolongation';
    const FIELD_TRANSLATIONS                = 'translations';
    const FIELD_SUBSTRING                   = 'substring';
    const FIELD_PRODUCTS                    = 'products';
    const FIELD_NUM_FOUND_PRODUCTS          = 'numFoundProducts';
    const FIELD_RESULTS_FOR                 = 'resultsFor';
    const FIELD_SHOP_COUNTRY_CODE           = 'shopCountryCode';

    const FIELD_INSTALLATION_DATE        = 'installationDate';

    const FIELD_CORE_KEY_VALUE        = 'keyValue';
    const FIELD_CORE_KEY_AUTHOR       = 'author';
    const FIELD_CORE_KEY_NAME         = 'name';
    const FIELD_CORE_KEY_EXPIRATION   = 'expiration';
    const FIELD_CORE_KEY_DATA         = 'keyData';
    const FIELD_CORE_KEY_EDITION_NAME = 'editionName';
    const FIELD_CORE_KEY_EXP_DATE     = 'expDate';
    const FIELD_CORE_KEY_PROLONG_KEY  = 'prolongKey';
    const FIELD_CORE_KEY_WAVE         = 'wave';
    const FIELD_CORE_KEY_XB_PRODUCTID = 'xbProductId';

    const FIELD_TAG_NAME                   = 'tag_name';
    const FIELD_TAG_BANNER_EXPIRATION_DATE = 'tag_banner_expiration_date';
    const FIELD_TAG_BANNER_IMG             = 'tag_banner_img';
    const FIELD_TAG_MODULE_BANNER          = 'tag_module_banner';
    const FIELD_TAG_BANNER_URL             = 'tag_banner_url';

    const FIELD_NOTIFICATION_TYPE        = 'type';
    const FIELD_NOTIFICATION_MODULE      = 'module';
    const FIELD_NOTIFICATION_IMAGE       = 'image';
    const FIELD_NOTIFICATION_TITLE       = 'title';
    const FIELD_NOTIFICATION_DESCRIPTION = 'description';
    const FIELD_NOTIFICATION_LINK        = 'link';
    const FIELD_NOTIFICATION_DATE        = 'date';
    const FIELD_NOTIFICATION_PAGE_PARAMS = 'pageParams';
    const FIELD_NOTIFICATION_PARAM_KEY   = 'paramKey';
    const FIELD_NOTIFICATION_PARAM_VALUE = 'paramValue';

    const FIELD_SECTION_TYPE         = 'type';
    const FIELD_SECTION_POS          = 'position';
    const FIELD_SECTION_TAG          = 'tag';
    const FIELD_SECTION_IMAGE        = 'imageURL';
    const FIELD_SECTION_ADDON        = 'addon';
    const FIELD_SECTION_BANNER       = 'bannerURL';
    const FIELD_SECTION_HTML         = 'html';
    const FIELD_SECTION_CSS          = 'css';
    const FIELD_SECTION_TRANSLATIONS = 'translations';
    const FIELD_SECTION_ADDONS       = 'addons';

    const FIELD_EDITION_NAME           = 'name';
    const FIELD_EDITION_XB_ID          = 'xb_product_id';
    const FIELD_EDITION_IS_CLOUD       = 'is_cloud';
    const FIELD_EDITION_DESCRIPTION    = 'description';
    const FIELD_EDITION_PRICE          = 'price';
    const FIELD_EDITION_AVAIL_FOR_SALE = 'avail_for_sale';
    const FIELD_EDITION_XCN_PLAN       = 'xcnPlan';

    const INACTIVE_KEYS = 'inactiveMPKeys';

    /**
     * Marketplace API version
     */
    const MP_API_VERSION      = '2.6';
    const XC_FREE_LICENSE_KEY = 'XC5-FREE-LICENSE';

    /**
     * Some regexps
     */
    const REGEXP_VERSION  = '/\d+\.?[\w\-\.]*/';
    const REGEXP_WORD     = '/[\w\"\']+/';
    const REGEXP_NUMBER   = '/\d+/';
    const REGEXP_HASH     = '/\w{32}/';
    const REGEXP_CURRENCY = '/[A-Z]{1,3}/';
    const REGEXP_CLASS    = '/[\w\\\\]+/';
    const REGEXP_TEXT     = '/([^<>]+)/';

    /**
     * Error codes
     */
    const ERROR_CODE_REFUND                  = 1030;
    const ERROR_CODE_FREE_LICENSE_REGISTERED = 3090;

    /**
     * HTTP request TTL
     */
    const REQUEST_TTL = 30;

    /**
     * HTTP request TTL for long actions
     */
    const REQUEST_LONG_TTL = 60;

    /**
     * Get long actions
     *
     * @return array
     */
    public static function getLongActions()
    {
        return [
            static::ACTION_GET_CORE_PACK,
            static::ACTION_GET_CORE_HASH,
            static::ACTION_GET_ADDON_PACK,
            static::ACTION_GET_ADDON_HASH,
            static::ACTION_GET_ADDON_INFO,
            static::ACTION_GET_ADDONS_LIST,
        ];
    }
}
