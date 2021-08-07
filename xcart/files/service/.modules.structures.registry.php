# <?php if (!defined('LC_DS')) { die(); } ?>

Amazon-PayWithAmazon:
    tables: {  }
    columns: { profiles: { socialLoginProvider: 'socialLoginProvider VARCHAR(128) DEFAULT NULL', socialLoginId: 'socialLoginId VARCHAR(128) DEFAULT NULL' } }
    dependencies: {  }
CDev-AustraliaPost:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-AuthorizeNet:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-Bestsellers:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-ContactUs:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-Coupons:
    tables: [coupons, product_class_coupons, membership_coupons, zone_coupons, coupon_categories, coupon_products, order_coupons]
    columns: {  }
    dependencies: {  }
CDev-Egoods:
    tables: [order_item_private_attachments, product_attachment_history_points]
    columns: { product_attachments: { private: 'private TINYINT(1) NOT NULL' } }
    dependencies: {  }
CDev-FeaturedProducts:
    tables: [featured_products]
    columns: {  }
    dependencies: {  }
CDev-FedEx:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-FileAttachments:
    tables: [product_attachment_translations, product_attachments, product_attachment_storages]
    columns: {  }
    dependencies: {  }
CDev-GoSocial:
    tables: {  }
    columns: { product_translations: { ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' }, sale_discounts: { useCustomOG: 'useCustomOG TINYINT(1) NOT NULL', ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' }, category_translations: { ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' }, products: { useCustomOG: 'useCustomOG TINYINT(1) NOT NULL' }, categories: { ogMeta: 'ogMeta LONGTEXT DEFAULT NULL', useCustomOG: 'useCustomOG TINYINT(1) NOT NULL' }, page_translations: { ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' }, sale_discount_translations: { ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' }, pages: { useCustomOG: 'useCustomOG TINYINT(1) NOT NULL', ogMeta: 'ogMeta LONGTEXT DEFAULT NULL', showSocialButtons: 'showSocialButtons TINYINT(1) NOT NULL' } }
    dependencies: { CDev-Sale: { sale_discounts: { useCustomOG: 'useCustomOG TINYINT(1) NOT NULL', ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' }, sale_discount_translations: { ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' } }, CDev-SimpleCMS: { page_translations: { ogMeta: 'ogMeta LONGTEXT DEFAULT NULL' }, pages: { useCustomOG: 'useCustomOG TINYINT(1) NOT NULL', ogMeta: 'ogMeta LONGTEXT DEFAULT NULL', showSocialButtons: 'showSocialButtons TINYINT(1) NOT NULL' } } }
CDev-GoogleAnalytics:
    tables: {  }
    columns: { order_items: { categoryAdded: 'categoryAdded VARCHAR(255) DEFAULT NULL' }, profiles: { gaClientId: 'gaClientId VARCHAR(255) NOT NULL' } }
    dependencies: {  }
CDev-MarketPrice:
    tables: {  }
    columns: { products: { marketPrice: 'marketPrice NUMERIC(14, 4) NOT NULL' } }
    dependencies: {  }
CDev-PINCodes:
    tables: [pin_codes]
    columns: { products: { pinCodesEnabled: 'pinCodesEnabled TINYINT(1) NOT NULL', autoPinCodes: 'autoPinCodes TINYINT(1) NOT NULL' } }
    dependencies: {  }
CDev-Paypal:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-ProductAdvisor:
    tables: [product_stats]
    columns: {  }
    dependencies: {  }
CDev-Quantum:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-Sale:
    tables: [sale_discount_products, sale_discounts, product_class_sale_discounts, membership_sale_discounts, category_sale_discounts, sale_discount_translations]
    columns: { products: { participateSale: 'participateSale TINYINT(1) NOT NULL', discountType: 'discountType VARCHAR(32) NOT NULL', salePriceValue: 'salePriceValue NUMERIC(14, 4) NOT NULL' }, clean_urls: { sale_discount_id: 'sale_discount_id INT UNSIGNED DEFAULT NULL' } }
    dependencies: {  }
CDev-SalesTax:
    tables: [sales_tax_translations, sales_tax_rates, sales_taxes]
    columns: {  }
    dependencies: {  }
CDev-SimpleCMS:
    tables: [page_images, menus, page_translations, menu_translations, menu_quick_flags, pages]
    columns: { clean_urls: { page_id: 'page_id INT UNSIGNED DEFAULT NULL' } }
    dependencies: {  }
CDev-TwoCheckout:
    tables: {  }
    columns: {  }
    dependencies: {  }
CDev-USPS:
    tables: [usps_shipment]
    columns: {  }
    dependencies: {  }
CDev-UserPermissions:
    tables: {  }
    columns: { roles: { enabled: 'enabled TINYINT(1) NOT NULL' } }
    dependencies: {  }
CDev-VolumeDiscounts:
    tables: [volume_discounts, zones_volume_discounts]
    columns: {  }
    dependencies: {  }
CDev-XMLSitemap:
    tables: {  }
    columns: {  }
    dependencies: {  }
Kliken-GoogleAds:
    tables: {  }
    columns: {  }
    dependencies: {  }
QSL-AuthorizenetAcceptjs:
    tables: {  }
    columns: {  }
    dependencies: {  }
QSL-BraintreeVZ:
    tables: {  }
    columns: { profiles: { braintree_customer_id: 'braintree_customer_id VARCHAR(255) NOT NULL', saveCardBoxChecked: 'saveCardBoxChecked TINYINT(1) NOT NULL' } }
    dependencies: {  }
QSL-CloudSearch:
    tables: {  }
    columns: { products: { csLastUpdate: 'csLastUpdate INT NOT NULL' }, categories: { csLastUpdate: 'csLastUpdate INT NOT NULL' } }
    dependencies: {  }
QSL-FlyoutCategoriesMenu:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-BulkEditing:
    tables: {  }
    columns: { products: { xcPendingBulkEdit: 'xcPendingBulkEdit TINYINT(1) NOT NULL' } }
    dependencies: {  }
XC-CanadaPost:
    tables: [capost_delivery_services, capost_delivery_service_options, capost_returns, k, capost_return_link_storage, capost_return_items, capost_return_links, order_capost_parcel_shipment_link_storage, order_capost_parcel_shipment_tracking, order_capost_parcel_shipment_links, order_capost_parcel_shipment_tracking_files, order_capost_parcel_shipment_tracking_events, order_capost_parcel_shipment_tracking_options, order_capost_parcel_manifests, order_capost_parcel_shipments_manifests, order_capost_parcel_shipment, order_capost_parcel_items, order_capost_parcel_manifest_link_storage, order_capost_parcel_manifest_links, order_capost_office, order_capost_parcels]
    columns: {  }
    dependencies: {  }
XC-Concierge:
    tables: {  }
    columns: { profiles: { conciergeUserId: 'conciergeUserId VARCHAR(128) DEFAULT NULL' } }
    dependencies: {  }
XC-CrispWhiteSkin:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-CustomOrderStatuses:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-CustomProductTabs:
    tables: [product_tabs, custom_global_tab_translation, custom_global_tabs, product_tab_translations]
    columns: { global_product_tabs: { enabled: 'enabled TINYINT(1) NOT NULL', link: 'link VARCHAR(255) DEFAULT NULL' } }
    dependencies: {  }
XC-CustomerAttachments:
    tables: [customer_attachments_storage]
    columns: { products: { isCustomerAttachmentsAvailable: 'isCustomerAttachmentsAvailable TINYINT(1) NOT NULL', isCustomerAttachmentsRequired: 'isCustomerAttachmentsRequired TINYINT(1) NOT NULL' } }
    dependencies: {  }
XC-EPDQ:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-ESelectHPP:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-FacebookMarketing:
    tables: {  }
    columns: { products: { facebookMarketingEnabled: 'facebookMarketingEnabled TINYINT(1) DEFAULT ''1'' NOT NULL' } }
    dependencies: {  }
XC-FastLaneCheckout:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-FreeShipping:
    tables: {  }
    columns: { shipping_methods: { free: 'free TINYINT(1) NOT NULL' }, products: { freeShip: 'freeShip TINYINT(1) NOT NULL', shipForFree: 'shipForFree TINYINT(1) NOT NULL', freightFixedFee: 'freightFixedFee NUMERIC(14, 4) NOT NULL' } }
    dependencies: {  }
XC-FroalaEditor:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-Geolocation:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-GoogleFeed:
    tables: {  }
    columns: { products: { googleFeedEnabled: 'googleFeedEnabled TINYINT(1) DEFAULT ''1'' NOT NULL' }, attributes: { googleShoppingGroup: 'googleShoppingGroup VARCHAR(255) DEFAULT NULL' } }
    dependencies: {  }
XC-IdealPayments:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-MailChimp:
    tables: [mailchimp_list_group_name, mailchimp_profile_interests, mailchimp_store, mailchimp_list_segments, segment_membership, segment_products, mailchimp_segment_subscriptions, mailchimp_lists, mailchimp_subscriptions, mailchimp_list_group]
    columns: { products: { useAsSegmentCondition: 'useAsSegmentCondition TINYINT(1) NOT NULL' }, orders: { mailchimpStoreId: 'mailchimpStoreId VARCHAR(255) NOT NULL' } }
    dependencies: {  }
XC-News:
    tables: [news_message_translations, news]
    columns: { clean_urls: { news_message_id: 'news_message_id INT UNSIGNED DEFAULT NULL' } }
    dependencies: {  }
XC-NewsletterSubscriptions:
    tables: [newsletter_subscriptions_subscribers]
    columns: {  }
    dependencies: {  }
XC-NotFinishedOrders:
    tables: {  }
    columns: { orders: { not_finished_order_id: 'not_finished_order_id INT DEFAULT NULL' } }
    dependencies: {  }
XC-Onboarding:
    tables: {  }
    columns: { products: { demo: 'demo TINYINT(1) NOT NULL' }, categories: { demo: 'demo TINYINT(1) NOT NULL' }, orders: { demo: 'demo TINYINT(1) NOT NULL' } }
    dependencies: {  }
XC-ProductComparison:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-ProductTags:
    tables: [product_tags, tags, tag_translations]
    columns: {  }
    dependencies: {  }
XC-RESTAPI:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-Reviews:
    tables: [reviews, order_review_keys]
    columns: {  }
    dependencies: {  }
XC-SagePay:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-Sitemap:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-Stripe:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-ThemeTweaker:
    tables: [theme_tweaker_template]
    columns: {  }
    dependencies: {  }
XC-UPS:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-UpdateInventory:
    tables: {  }
    columns: {  }
    dependencies: {  }
XC-Upselling:
    tables: [upselling_products]
    columns: {  }
    dependencies: {  }
XC-VendorMessages:
    tables: [vendor_convo_messages, vendor_convo_message_reads, conversations, conversation_members]
    columns: {  }
    dependencies: {  }
XPay-XPaymentsCloud:
    tables: [xpayments_subscription_plans, bscriptionPlan, xpayments_subscriptions, xpayments_fraud_check_data]
    columns: { order_items: { xpaymentsEmulated: 'xpaymentsEmulated TINYINT(1) NOT NULL', xpaymentsUniqueId: 'xpaymentsUniqueId VARCHAR(255) NOT NULL', subscriptionId: 'subscriptionId INT UNSIGNED DEFAULT NULL COMMENT ''Unique id''' }, profiles: { xpaymentsCustomerId: 'xpaymentsCustomerId VARCHAR(255) NOT NULL' }, orders: { xpaymentsFraudStatus: 'xpaymentsFraudStatus VARCHAR(255) NOT NULL', xpaymentsFraudType: 'xpaymentsFraudType VARCHAR(255) NOT NULL', xpaymentsFraudCheckTransactionId: 'xpaymentsFraudCheckTransactionId INT NOT NULL', buyWithApplePay: 'buyWithApplePay TINYINT(1) NOT NULL' } }
    dependencies: {  }
creator-wishlist:
    tables: {  }
    columns: {  }
    dependencies: {  }
